<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApicoidOngkirService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('regional.apicoid.base_url');
        $this->apiKey = config('regional.apicoid.key');
    }

    public function getShippingCost(string $originVillageCode, string $destinationVillageCode, int $weight)
    {
        $response = Http::withHeaders([
            'x-api-co-id' => $this->apiKey,
        ])->withQueryParameters([
            'origin_village_code'  => $originVillageCode,
            'destination_village_code' => $destinationVillageCode,
            'weight' => $weight,
        ])
            ->get($this->baseUrl . '/expedition/shipping-cost');

        return $response->json();
    }
}
