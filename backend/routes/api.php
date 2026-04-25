<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('invoices', InvoiceController::class)->only([
        'index', 'store', 'show'
    ]);

    // NEW
    Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send']);
    Route::post('/invoices/{invoice}/view', [InvoiceController::class, 'view']);
    Route::post('/invoices/{invoice}/pay', [PaymentController::class, 'store']);

    Route::post('/invoices/{invoice}/create-order', [PaymentController::class, 'createOrder']);
});

Route::post('/webhooks/razorpay', [WebhookController::class, 'handleRazorpay']);
