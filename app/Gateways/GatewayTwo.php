<?php

namespace App\Gateways;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Exception;

class GatewayTwo implements PaymentGatewayInterface
{
    public function pay(array $data): array
    {
        $response = Http::timeout(5)->withHeaders([
            'Gateway-Auth-Token'  => 'tk_f2198cc671b5289fa856',
            'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
        ])->post("http://payment-mocks:3002/transacoes", [
            'valor'        => (int) $data['amount'],
            'nome'         => $data['client_name'],
            'email'        => $data['client_email'],
            'numeroCartao' => $data['card_number'],
            'cvv'          => $data['cvv']
        ]);

        if ($response->failed()) {
            throw new Exception("Falha no Gateway 2");
        }

        return [
            'success'     => true,
            'external_id' => $response->json()['id'] ?? uniqid(),
            'gateway'     => 'Gateway 2'
        ];
    }
}
