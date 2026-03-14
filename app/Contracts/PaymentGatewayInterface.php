<?php

namespace App\Contracts;

interface PaymentGatewayInterface
{
    public function pay(array $data): array;
}
