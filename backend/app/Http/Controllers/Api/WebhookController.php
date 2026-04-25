<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function handleRazorpay(Request $request)
    {
        
        $payload = $request->all();
        $signature = $request->header('X-Razorpay-Signature');

        // $generatedSignature = hash_hmac(
        //     'sha256',
        //     $request->getContent(),
        //     config('services.razorpay.webhook_secret')
        // );

        // if ($generatedSignature !== $signature) {
        //     return response()->json(['message' => 'Invalid signature'], 400);
        // }

        // TODO: verify signature (IMPORTANT for production)

        if ($payload['event'] === 'payment.captured') {

            $entity = $payload['payload']['payment']['entity'];

            $invoiceNumber = $entity['notes']['invoice_number'] ?? null;

            if (!$invoiceNumber) {
                return response()->json(['message' => 'Invoice not found'], 400);
            }

            $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

            if (!$invoice) {
                return response()->json(['message' => 'Invalid invoice'], 404);
            }

            $this->paymentService->recordPayment($invoice, [
                'amount' => $entity['amount'] / 100,
                'gateway' => 'razorpay',
                'transaction_id' => $entity['id'],
                'payload' => $payload,
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
