<?php

namespace App\Services;

use App\Models\Client;
use App\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\Collection;

class ClientService
{
    public function __construct(
        protected ClientRepository $clientRepository
    ) {
    }

    public function list(int $tenantId): Collection
    {
        return $this->clientRepository->getAllByTenant($tenantId);
    }

    public function create(int $tenantId, array $data): Client
    {
        $data['tenant_id'] = $tenantId;

        return $this->clientRepository->create($data);
    }

    public function findOrFail(int $id, int $tenantId): Client
    {
        return $this->clientRepository->findByIdAndTenant($id, $tenantId)
            ?? abort(404, 'Client not found.');
    }

    public function update(Client $client, array $data): Client
    {
        return $this->clientRepository->update($client, $data);
    }

    public function delete(Client $client): bool
    {
        return $this->clientRepository->delete($client);
    }
}