<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function recordPayment(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $payment = Payment::create([
                'tenant_id' => $invoice->tenant_id,
                'invoice_id' => $invoice->id,
                'gateway' => $data['gateway'] ?? 'manual',
                'gateway_transaction_id' => $data['transaction_id'] ?? null,
                'amount' => $data['amount'],
                'status' => 'success',
                'paid_at' => now(),
                'gateway_payload' => $data,
            ]);

            // update invoice paid amount
            $invoice->paid_amount += $payment->amount;

            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->status = 'paid';
                $invoice->paid_at = now();
            }

            $invoice->save();

            return $payment;
        });
    }
}