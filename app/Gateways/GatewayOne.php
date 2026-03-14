<?php

namespace App\Gateways;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Exception;

class GatewayOne implements PaymentGatewayInterface
{
    public function pay(array $data): array
    {
        $response = Http::timeout(5)->post("http://payment-mocks:3001/transactions", [
            'amount'     => (int) $data['amount'],
            'name'       => $data['client_name'],
            'email'      => $data['client_email'],
            'cardNumber' => $data['card_number'],
            'cvv'        => $data['cvv']
        ]);

        if ($response->failed()) {
            throw new Exception("Falha no Gateway 1");
        }

        return [
            'success'     => true,
            'external_id' => $response->json()['id'] ?? uniqid(),
            'gateway'     => 'Gateway 1'
        ];
    }
}
