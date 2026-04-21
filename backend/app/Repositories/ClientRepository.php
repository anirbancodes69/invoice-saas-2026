<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository
{
    public function getAllByTenant(int $tenantId): Collection
    {
        return Client::where('tenant_id', $tenantId)->latest()->get();
    }

    public function create(array $data): Client
    {
        return Client::create($data);
    }

    public function findByIdAndTenant(int $id, int $tenantId): ?Client
    {
        return Client::where('tenant_id', $tenantId)->where('id', $id)->first();
    }

    public function update(Client $client, array $data): Client
    {
        $client->update($data);
        return $client->refresh();
    }

    public function delete(Client $client): bool
    {
        return (bool) $client->delete();
    }
}