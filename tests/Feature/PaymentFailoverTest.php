<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Client;

class PaymentFailoverTest extends TestCase
{
    use RefreshDatabase; // Limpa o banco a cada teste para garantir isolamento

    /**
     * Teste de Failover e Cálculo: Garante que se o Gateway 1 falhar,
     * o sistema usa o Gateway 2 e salva o valor total correto (Preço x Quantidade).
     */
    public function test_deve_usar_gateway_2_quando_gateway_1_falhar_e_calcular_valor_total()
    {
        // 1. Criar dados: Produto custa 10.00
        $product = Product::create(['name' => 'Produto Teste', 'amount' => 10.00]);
        $client = Client::create(['name' => 'Anderson Teste', 'email' => 'teste@anderson.com']);

        // 2. Simular falha no Gateway 1 e sucesso no Gateway 2
        Http::fake([
            'http://payment-mocks:3001/*' => Http::response(['error' => 'Gateway 1 Offline'], 500),
            'http://payment-mocks:3002/*' => Http::response(['id' => 'trans_mock_contingencia_123'], 201),
        ]);

        // 3. Executar com QUANTIDADE = 3 (Total deve ser 30.00)
        $payload = [
            'product_id'  => $product->id,
            'client_id'   => $client->id,
            'card_number' => '1234567812345678',
            'cvv'         => '123',
            'quantity'    => 3
        ];

        $response = $this->postJson('/api/purchase', $payload);

        // 4. Verificações (Assertions)
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'gateway_utilizado' => 'Gateway 2',
            'message' => 'Compra realizada com sucesso!'
        ]);

        // Verifica se salvou o valor total calculado (10.00 * 3 = 30.00)
        $this->assertDatabaseHas('transactions', [
            'amount'      => 30.00,
            'gateway'     => 'Gateway 2',
            'external_id' => 'trans_mock_contingencia_123'
        ]);

        // Verifica se a tabela de relação salvou a quantidade certa
        $this->assertDatabaseHas('transaction_products', [
            'product_id' => $product->id,
            'quantity'   => 3,
            'amount'     => 10.00
        ]);
    }

    /**
     * Teste de Erro Crítico: Garante que o sistema retorna erro 500 se nenhum gateway funcionar.
     */
    public function test_deve_retornar_erro_se_ambos_os_gateways_falharem()
    {
        $product = Product::create(['name' => 'Produto Teste', 'amount' => 10.00]);
        $client = Client::create(['name' => 'Anderson Teste', 'email' => 'teste@anderson.com']);

        Http::fake([
            'http://payment-mocks:3001/*' => Http::response([], 500),
            'http://payment-mocks:3002/*' => Http::response([], 500),
        ]);

        $payload = [
            'product_id'  => $product->id,
            'client_id'   => $client->id,
            'card_number' => '1234567812345678',
            'cvv'         => '123'
        ];

        $response = $this->postJson('/api/purchase', $payload);

        $response->assertStatus(500);
        $response->assertJsonStructure(['error']);
    }

    /**
     * Teste de Validação: Impede que a compra prossiga com quantidades inválidas.
     */
    public function test_nao_deve_permitir_compra_com_quantidade_invalida()
    {
        $product = Product::create(['name' => 'Produto Teste', 'amount' => 10.00]);
        $client = Client::create(['name' => 'Anderson Teste', 'email' => 'teste@anderson.com']);

        $payload = [
            'product_id'  => $product->id,
            'client_id'   => $client->id,
            'card_number' => '1234567812345678',
            'cvv'         => '123',
            'quantity'    => 0 // Quantidade mínima deve ser 1
        ];

        $response = $this->postJson('/api/purchase', $payload);

        $response->assertStatus(422); // Erro de validação
    }
}
