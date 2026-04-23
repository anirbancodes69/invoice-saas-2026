<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected InvoiceService $invoiceService
    ) {
    }

    public function store(Request $request, int $invoiceId): JsonResponse
    {
        $invoice = $this->invoiceService->findOrFail(
            $invoiceId,
            auth()->user()->tenant_id
        );

        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $payment = $this->paymentService->recordPayment($invoice, $request->all());

        return response()->json([
            'message' => 'Payment recorded successfully.',
            'data' => $payment,
        ]);
    }
}