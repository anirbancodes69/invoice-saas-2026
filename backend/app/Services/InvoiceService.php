<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        protected InvoiceRepository $invoiceRepository
    ) {
    }

    public function list(int $tenantId)
    {
        return $this->invoiceRepository->getAllByTenant($tenantId);
    }

    public function create(int $tenantId, array $data): Invoice
    {
        return DB::transaction(function () use ($tenantId, $data) {
            $subtotal = collect($data['items'])->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $taxAmount = $data['tax_amount'] ?? 0;
            $discountAmount = $data['discount_amount'] ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            $invoice = $this->invoiceRepository->create([
                'tenant_id' => $tenantId,
                'client_id' => $data['client_id'],
                'invoice_number' => $this->generateInvoiceNumber(),
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'status' => InvoiceStatus::DRAFT->value,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'currency' => $data['currency'] ?? 'INR',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_name' => $item['item_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return $invoice->load(['client', 'items']);
        });
    }

    public function findOrFail(int $id, int $tenantId): Invoice
    {
        return $this->invoiceRepository->findByIdAndTenant($id, $tenantId)
            ?? abort(404, 'Invoice not found.');
    }

    protected function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('YmdHis') . '-' . random_int(100, 999);
    }
}