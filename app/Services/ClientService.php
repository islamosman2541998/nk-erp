<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    public function create(array $data): Client
    {
        $data['created_by'] = Auth::id();

        return Client::create($data);
    }

    public function update(Client $client, array $data): Client
    {
        $data['updated_by'] = Auth::id();

        $client->update($data);

        return $client;
    }

    public function delete(Client $client): void
    {
        $client->delete();
    }
}