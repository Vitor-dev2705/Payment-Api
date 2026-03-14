<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Verifica se o utilizador está logado e se tem o papel necessário
        if (!$request->user() || $request->user()->role !== $role) {
            return response()->json(['error' => 'Acesso negado. Requer nível: ' . $role], 403);
        }

        return $next($request);
    }
}
