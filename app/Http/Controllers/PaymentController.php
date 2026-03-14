<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * @OA\Tag(name="Pagamentos", description="Endpoints para processamento de compras")
 */
class PaymentController extends Controller
{
    /**
     * @OA\Post(
     * path="/purchase",
     * summary="Realizar uma compra",
     * security={{"bearerAuth":{}}},
     * description="Processa o pagamento através de múltiplos gateways com failover automático.",
     * tags={"Pagamentos"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"product_id", "client_id", "card_number", "cvv"},
     * @OA\Property(property="product_id", type="integer", example=1),
     * @OA\Property(property="client_id", type="integer", example=1),
     * @OA\Property(property="card_number", type="string", example="5569000000006063"),
     * @OA\Property(property="cvv", type="string", example="010"),
     * @OA\Property(property="quantity", type="integer", example=1)
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Compra realizada com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string"),
     * @OA\Property(property="transaction_id", type="integer"),
     * @OA\Property(property="gateway_utilizado", type="string"),
     * @OA\Property(property="total_pago", type="number")
     * )
     * ),
     * @OA\Response(response=500, description="Erro no processamento do pagamento")
     * )
     */
    public function store(Request $request, PaymentService $paymentService)
    {
        $request->validate([
            'product_id'  => 'required|exists:products,id',
            'client_id'   => 'required|exists:clients,id',
            'card_number' => 'required|string|size:16',
            'cvv'         => 'required|string|size:3',
            'quantity'    => 'integer|min:1',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $client  = Client::findOrFail($request->client_id);

            $quantity = $request->input('quantity', 1);
            $totalAmount = $product->amount * $quantity;

            $paymentData = [
                'amount'       => (int) ($totalAmount * 100),
                'client_name'  => $client->name,
                'client_email' => $client->email,
                'card_number'  => $request->card_number,
                'cvv'          => $request->cvv,
            ];

            $gatewayResponse = $paymentService->process($paymentData);

            $transaction = DB::transaction(function () use ($product, $client, $gatewayResponse, $request, $quantity, $totalAmount) {
                $newTransaction = Transaction::create([
                    'client_id'         => $client->id,
                    'product_id'        => $product->id,
                    'quantity'          => $quantity,
                    'amount'            => $totalAmount,
                    'status'            => 'paid',
                    'gateway'           => $gatewayResponse['gateway'],
                    'external_id'       => $gatewayResponse['external_id'],
                    'card_last_numbers' => substr($request->card_number, -4),
                ]);

                TransactionProduct::create([
                    'transaction_id' => $newTransaction->id,
                    'product_id'     => $product->id,
                    'quantity'       => $quantity,
                    'amount'         => $product->amount,
                ]);

                return $newTransaction;
            });

            return response()->json([
                'message' => 'Compra realizada com sucesso!',
                'transaction_id' => $transaction->id,
                'gateway_utilizado' => $transaction->gateway,
                'external_id' => $transaction->external_id,
                'total_pago' => $transaction->amount
            ], 201);

        } catch (Exception $e) {
            return response()->json(['error' => 'Falha no processamento: ' . $e->getMessage()], 500);
        }
    }
}
