<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(name="Autenticação", description="Endpoints para login e geração de tokens")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/login",
     * summary="Autenticar usuário e gerar token de acesso",
     * tags={"Autenticação"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email", "password"},
     * @OA\Property(property="email", type="string", format="email", example="admin@betalent.tech"),
     * @OA\Property(property="password", type="string", format="password", example="password")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login realizado com sucesso",
     * @OA\JsonContent(
     * @OA\Property(property="token", type="string", example="1|AbCdeFgHiJ..."),
     * @OA\Property(property="role", type="string", example="admin")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Credenciais incorretas ou erro de validação"
     * )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken,
            'role' => $user->role
        ]);
    }
}
