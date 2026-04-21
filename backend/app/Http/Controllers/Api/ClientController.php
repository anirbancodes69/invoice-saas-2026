<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
   public function __construct(
        protected ClientService $clientService
    ) {
    }

    public function index(): JsonResponse
    {
        $clients = $this->clientService->list(auth()->user()->tenant_id);

        return response()->json([
            'data' => ClientResource::collection($clients),
        ]);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = $this->clientService->create(
            auth()->user()->tenant_id,
            $request->validated()
        );

        return response()->json([
            'message' => 'Client created successfully.',
            'data' => new ClientResource($client),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $client = $this->clientService->findOrFail($id, auth()->user()->tenant_id);

        return response()->json([
            'data' => new ClientResource($client),
        ]);
    }

    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        $client = $this->clientService->findOrFail($id, auth()->user()->tenant_id);
        $client = $this->clientService->update($client, $request->validated());

        return response()->json([
            'message' => 'Client updated successfully.',
            'data' => new ClientResource($client),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $client = $this->clientService->findOrFail($id, auth()->user()->tenant_id);
        $this->clientService->delete($client);

        return response()->json([
            'message' => 'Client deleted successfully.',
        ]);
    }
}
