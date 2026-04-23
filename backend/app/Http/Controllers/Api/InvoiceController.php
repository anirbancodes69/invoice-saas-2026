<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    public function index(): JsonResponse
    {
        $invoices = $this->invoiceService->list(auth()->user()->tenant_id);

        return response()->json([
            'data' => InvoiceResource::collection($invoices),
        ]);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->create(
            auth()->user()->tenant_id,
            $request->validated()
        );

        return response()->json([
            'message' => 'Invoice created successfully.',
            'data' => new InvoiceResource($invoice),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->findOrFail($id, auth()->user()->tenant_id);

        return response()->json([
            'data' => new InvoiceResource($invoice),
        ]);
    }

    public function send(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->findOrFail($id, auth()->user()->tenant_id);

        $invoice = $this->invoiceService->markAsSent($invoice);

        return response()->json([
            'message' => 'Invoice sent successfully.',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    public function view(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->findOrFail($id, auth()->user()->tenant_id);

        $invoice = $this->invoiceService->markAsViewed($invoice);

        return response()->json([
            'message' => 'Invoice viewed.',
            'data' => new InvoiceResource($invoice),
        ]);
    }
}
