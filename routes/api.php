<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\ClientController;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/purchase', [PaymentController::class, 'store']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/{id}', [ClientController::class, 'show']);

    Route::middleware('role:admin')->group(function () {
        Route::patch('/gateways/{gateway}/status', [GatewayController::class, 'toggleStatus']);
        Route::patch('/gateways/{gateway}/priority', [GatewayController::class, 'updatePriority']);

        Route::post('/transactions/{id}/refund', [PaymentController::class, 'refund']);

        Route::apiResource('users', UserController::class);
        Route::apiResource('products', ProductController::class)->except(['index']);
    });
});
