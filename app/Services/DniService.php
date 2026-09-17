<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DniService
{
    public function consultar(string $dni): ?array
    {
        $response = Http::withToken(config('services.apiperu.token'))
            ->acceptJson()
            ->post('https://api.apiperu.dev/dni', [
                'dni' => $dni,
            ]);

        if ($response->successful() && $response->json('success')) {
            return $response->json('data');
        }

        return null;
    }
}