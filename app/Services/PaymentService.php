<?php

namespace App\Services;

use App\Models\Gateway;
use App\Gateways\GatewayOne;
use App\Gateways\GatewayTwo;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Processa o pagamento tentando os gateways ativos por ordem de prioridade.
     */
    public function process(array $data): array
    {
        // Busca gateways ativos no banco ordenados pela prioridade (1, 2, 3...)
        $activeGateways = Gateway::where('is_active', true)
            ->orderBy('priority', 'asc')
            ->get();

        if ($activeGateways->isEmpty()) {
            throw new Exception("Nenhum gateway de pagamento disponível ou ativo no momento.");
        }

        foreach ($activeGateways as $config) {
            try {
                $gatewayInstance = $this->resolveGateway($config->name);

                // Tenta realizar o pagamento com o gateway da vez
                return $gatewayInstance->pay($data);

            } catch (Exception $e) {
                Log::warning("Falha no {$config->name}: " . $e->getMessage());

                // Continua o loop para tentar o próximo gateway da lista de prioridade
                continue;
            }
        }

        // Se o loop terminar sem um 'return', significa que todos falharam
        throw new Exception("Todos os gateways configurados falharam no processamento.");
    }

    /**
     * Instancia a classe correta baseada no nome salvo no banco de dados.
     */
    private function resolveGateway(string $name)
    {
        return match ($name) {
            'Gateway 1' => new GatewayOne(),
            'Gateway 2' => new GatewayTwo(),
            default     => throw new Exception("Gateway '{$name}' não possui implementação técnica."),
        };
    }
}
