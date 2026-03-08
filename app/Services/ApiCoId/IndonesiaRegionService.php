<?php

namespace App\Services\ApiCoId;

use App\Settings\CourierSettings;
use Illuminate\Support\Facades\Http;

class IndonesiaRegionService
{
    private string $baseUrl = '';
    private string $apiKey = '';

    public function __construct()
    {
        $this->apiKey = app(CourierSettings::class)->apicoid_api_key;
        $this->baseUrl = app(CourierSettings::class)->apicoid_base_url;
    }

    public function getProvinces()
    {
        $response = Http::withHeaders([
            'x-api-co-id' => $this->apiKey
        ])->get($this->baseUrl . '/regional/indonesia/provinces');
        return $response->json();
    }
}
