<?php

namespace App\Jobs;

use App\Models\Country;
use App\Models\Province;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetProvinceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::get('https://vanrezky.github.io/api-wilayah-indonesia/api/provinces.json');

            if ($response->failed()) {
                throw new Exception("Failed to get response data provinces from server");
            }

            $provinces = $response->collect();
            $country = Country::where('iso','ID')->first();

            $insertProvinces = [];
            foreach ($provinces as $province) {
                $insertProvinces[] = [
                    'country_id' =>$country->id,
                    'name' =>  $province['name'],
                    ''
                ]
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
