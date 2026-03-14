<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="Clientes", description="Gerenciamento de Clientes")
 */
class ClientController extends Controller
{
    /**
     * @OA\Get(
     * path="/clients",
     * summary="Listar todos os clientes",
     * security={{"bearerAuth":{}}},
     * tags={"Clientes"},
     * @OA\Response(
     * response=200,
     * description="Lista de clientes retornada com sucesso",
     * @OA\JsonContent(type="array", @OA\Items(type="object"))
     * ),
     * @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json(Client::all(), 200);
    }

    /**
     * @OA\Get(
     * path="/clients/{id}",
     * summary="Exibir detalhes de um cliente específico com suas transações",
     * security={{"bearerAuth":{}}},
     * tags={"Clientes"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID do cliente",
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Detalhes do cliente e transações"
     * ),
     * @OA\Response(response=404, description="Cliente não encontrado"),
     * @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            // Eager loading para performance
            $client = Client::with('transactions')->findOrFail($id);
            return response()->json($client, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Cliente não encontrado.'
            ], 404);
        }
    }
}
