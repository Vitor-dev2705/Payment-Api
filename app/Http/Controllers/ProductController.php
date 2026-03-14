<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Produtos", description="Gerenciamento de produtos")
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     * path="/products",
     * summary="Listar todos os produtos",
     * security={{"bearerAuth":{}}},
     * tags={"Produtos"},
     * @OA\Response(
     * response=200,
     * description="Lista de produtos cadastrados",
     * @OA\JsonContent(type="array", @OA\Items(type="object"))
     * ),
     * @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    /**
     * @OA\Post(
     * path="/products",
     * summary="Cadastrar um novo produto",
     * security={{"bearerAuth":{}}},
     * tags={"Produtos"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "amount", "stock"},
     * @OA\Property(property="name", type="string", example="Produto Teste"),
     * @OA\Property(property="amount", type="number", format="float", example=99.90),
     * @OA\Property(property="stock", type="integer", example=50)
     * )
     * ),
     * @OA\Response(response=201, description="Produto criado com sucesso"),
     * @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'amount' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    /**
     * @OA\Get(
     * path="/products/{id}",
     * summary="Exibir detalhes de um produto",
     * security={{"bearerAuth":{}}},
     * tags={"Produtos"},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Dados do produto"),
     * @OA\Response(response=404, description="Produto não encontrado")
     * )
     */
    public function show($id)
    {
        return response()->json(Product::findOrFail($id));
    }

    /**
     * @OA\Delete(
     * path="/products/{id}",
     * summary="Remover um produto",
     * security={{"bearerAuth":{}}},
     * tags={"Produtos"},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=204, description="Produto removido"),
     * @OA\Response(response=404, description="Produto não encontrado")
     * )
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
