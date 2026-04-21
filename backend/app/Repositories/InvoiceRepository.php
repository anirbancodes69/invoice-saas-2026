<?php

namespace App\Repositories;

use App\Models\Invoice;

class InvoiceRepository
{
    public function getAllByTenant(int $tenantId)
    {
        return Invoice::with(['client', 'items'])
            ->where('tenant_id', $tenantId)
            ->latest()
            ->get();
    }

    public function findByIdAndTenant(int $id, int $tenantId): ?Invoice
    {
        return Invoice::with(['client', 'items', 'payments'])
            ->where('tenant_id', $tenantId)
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);
        return $invoice->refresh();
    }

    public function delete(Invoice $invoice): bool
    {
        return (bool) $invoice->delete();
    }
}