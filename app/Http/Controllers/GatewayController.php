<?php

namespace App\Http\Controllers;

use App\Models\Gateway;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function toggleStatus(Gateway $gateway)
    {
        $gateway->update(['is_active' => !$gateway->is_active]);
        return response()->json(['message' => 'Status do gateway atualizado.', 'gateway' => $gateway]);
    }

    public function updatePriority(Request $request, Gateway $gateway)
    {
        $request->validate(['priority' => 'required|integer']);
        $gateway->update(['priority' => $request->priority]);
        return response()->json(['message' => 'Prioridade atualizada.', 'gateway' => $gateway]);
    }
}
