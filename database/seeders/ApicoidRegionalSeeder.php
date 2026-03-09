<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use App\Models\SubDistrict;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApicoidRegionalSeeder extends Seeder
{
    protected string $baseUrl;
    protected string $apiKey;
    protected array $unmatched = [];
    protected $indonesia;
    protected float $threshold = 85.0; // Similarity threshold in percent

    public function __construct()
    {
        $this->baseUrl = config('regional.apicoid.base_url', 'https://api.co.id');
        $this->apiKey = config('regional.apicoid.key');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (empty($this->apiKey)) {
            $this->command->error('APICOID_API_KEY is not set in .env or config/regional.php');
            return;
        }

        $this->indonesia = Country::where('iso', 'ID')->orWhere('name', 'Indonesia')->first();
        if (!$this->indonesia) {
            $this->command->error('Indonesia not found in countries table.');
            return;
        }

        $this->command->info('Starting APICOID Regional Seeding...');

        $this->syncProvinces();

        $this->logUnmatched();
        $this->command->info('Sync completed.');
    }

    protected function syncProvinces()
    {
        $this->command->info('Syncing Provinces...');
        $data = $this->fetchAll('/regional/indonesia/provinces');

        foreach ($data as $item) {
            $apiCode = $item['code'];
            $apiName = $item['name'];

            $province = $this->matchRecord(Province::class, $apiName, ['country_id' => $this->indonesia->id]);

            if (!$province) {
                $province = Province::create([
                    'country_id' => $this->indonesia->id,
                    'name' => $apiName,
                    'apicoid_code' => $apiCode,
                    'rajaongkir' => 0,
                ]);
                $this->command->info("Created Province: {$apiName}");
            } else {
                $province->update(['apicoid_code' => $apiCode]);
                $this->command->comment("Matched Province: {$apiName}");
            }

            // Optimization: If this is an existing province and all its districts already have codes,
            // we might still need to check deeper. But to save API calls, we only proceed if 
            // there's a reason to believe it's incomplete. 
            // Better: Let the deeper methods handle skipping.
            $this->syncDistricts($province, $apiCode);
        }
    }

    protected function syncDistricts(Province $province, string $provinceCode)
    {
        // Resume Logic: If all districts in DB for this province already have apicoid_code,
        // we could skip fetching from API IF we assume no new districts are added.
        // However, the seeder is also for ADDING missing data.
        // To balance, we check if we've processed this before.
        $hasEmptyDistricts = District::where('province_id', $province->id)->whereNull('apicoid_code')->exists();

        // If everything at this level is done, we still need to check sub-levels.
        // But we only fetch from API if we really need to.

        $data = $this->fetchAll("/regional/indonesia/provinces/{$provinceCode}/regencies");

        foreach ($data as $item) {
            $apiCode = $item['code'];
            $apiName = $item['name'];

            $district = $this->matchRecord(District::class, $apiName, ['province_id' => $province->id]);

            if (!$district) {
                $district = District::create([
                    'province_id' => $province->id,
                    'name' => $apiName,
                    'type' => Str::contains(strtolower($apiName), 'kota') ? 'Kota' : 'Kabupaten',
                    'apicoid_code' => $apiCode,
                    'rajaongkir' => 0,
                    'postal_code' => 0,
                ]);
                $this->command->info("  Created District: {$apiName}");
            } else {
                if ($district->apicoid_code !== $apiCode) {
                    $district->update(['apicoid_code' => $apiCode]);
                }
            }

            $this->syncSubDistricts($district, $apiCode);
        }
    }

    protected function syncSubDistricts(District $district, string $districtCode)
    {
        // Resume Logic: Skip if all subdistricts under this district already have codes
        // and we are just looking to fill gaps.
        $hasEmpty = SubDistrict::where('district_id', $district->id)->whereNull('apicoid_code')->exists();

        // If we want to be very aggressive in resuming, we could check if children are done.
        // But the most expensive call is syncVillages.

        $data = $this->fetchAll("/regional/indonesia/regencies/{$districtCode}/districts");

        foreach ($data as $item) {
            $apiCode = $item['code'];
            $apiName = $item['name'];

            $subDistrict = $this->matchRecord(SubDistrict::class, $apiName, ['district_id' => $district->id]);

            if (!$subDistrict) {
                $subDistrict = SubDistrict::create([
                    'district_id' => $district->id,
                    'name' => $apiName,
                    'apicoid_code' => $apiCode,
                    'rajaongkir' => 0,
                    'postal_code' => 0,
                ]);
                $this->command->info("    Created SubDistrict: {$apiName}");
            } else {
                if ($subDistrict->apicoid_code !== $apiCode) {
                    $subDistrict->update(['apicoid_code' => $apiCode]);
                }
            }

            $this->syncVillages($subDistrict, $apiCode);
        }
    }

    protected function syncVillages(SubDistrict $subDistrict, string $subDistrictCode)
    {
        // RESUME LOGIC: This is the most critical part (~80k villages).
        // If all villages in our DB for this sub-district already have apicoid_code, 
        // we SKIP the API call entirely for this sub-district.
        // This effectively "continues" from where it stopped.
        $exists = Village::where('sub_district_id', $subDistrict->id)->exists();
        $allMatched = $exists && !Village::where('sub_district_id', $subDistrict->id)->whereNull('apicoid_code')->exists();

        if ($allMatched) {
            // Optional: $this->command->line("      Skipping Villages for {$subDistrictCode} (Already synced)");
            return;
        }

        $data = $this->fetchAll("/regional/indonesia/districts/{$subDistrictCode}/villages");

        foreach ($data as $item) {
            $apiCode = $item['code'];
            $apiName = $item['name'];
            $postalCode = $item['postal_code'][0] ?? 0;

            $village = $this->matchRecord(Village::class, $apiName, ['sub_district_id' => $subDistrict->id]);

            if (!$village) {
                $village = Village::create([
                    'sub_district_id' => $subDistrict->id,
                    'name' => $apiName,
                    'apicoid_code' => $apiCode,
                    'rajaongkir' => 0,
                    'postal_code' => $postalCode,
                ]);
            } else {
                if ($village->apicoid_code !== $apiCode) {
                    $village->update(['apicoid_code' => $apiCode]);
                }
            }
        }
    }

    /**
     * Fetch all pages of data from the API.
     */
    protected function fetchAll(string $endpoint): array
    {
        $allData = [];
        $page = 1;
        $hasMore = true;

        while ($hasMore) {
            $response = $this->fetch($endpoint, ['page' => $page, 'limit' => 100]);

            if (!$response || !isset($response['data']) || !is_array($response['data'])) {
                $hasMore = false;
                continue;
            }

            $pageData = $response['data'];
            $allData = array_merge($allData, $pageData);

            // If we got fewer than 100 items, we've reached the end
            if (count($pageData) < 100) {
                $hasMore = false;
            } else {
                $page++;
                // Small sleep to prevent hitting rate limits too hard during loop
                usleep(100000); // 100ms
            }
        }

        return $allData;
    }

    protected function fetch(string $endpoint, array $query = [])
    {
        $response = Http::retry(3, 500)
            ->withHeaders(['x-api-co-id' => $this->apiKey])
            ->get($this->baseUrl . $endpoint, $query);

        if ($response->successful()) {
            $json = $response->json();
            if (isset($json['is_success']) && $json['is_success']) {
                return $json;
            }
            Log::warning("Apicoid API logic error on {$endpoint}: " . ($json['message'] ?? 'Unknown error'));
        }

        Log::error("Apicoid API Connection Error: {$response->status()} - {$response->body()} on {$endpoint}");
        return null;
    }

    protected function matchRecord(string $model, string $name, array $constraints)
    {
        $normalizedApiName = $this->normalizeName($name);

        // Load candidates only once for the parent constraint
        $candidates = $model::where($constraints)->get();

        // 1. Exact normalized match
        foreach ($candidates as $candidate) {
            if ($this->normalizeName($candidate->name) === $normalizedApiName) {
                return $candidate;
            }
        }

        // 2. Similarity match
        $bestMatch = null;
        $highestScore = 0;

        foreach ($candidates as $candidate) {
            similar_text($this->normalizeName($candidate->name), $normalizedApiName, $percent);
            if ($percent >= $this->threshold && $percent > $highestScore) {
                $highestScore = $percent;
                $bestMatch = $candidate;
            }
        }

        if (!$bestMatch) {
            $this->unmatched[] = [
                'level' => class_basename($model),
                'api_name' => $name,
                'normalized' => $normalizedApiName,
                'constraints' => $constraints
            ];
        }

        return $bestMatch;
    }

    protected function normalizeName(string $name): string
    {
        $name = strtolower($name);
        $replacements = [
            'kabupaten ' => '',
            'kab. ' => '',
            'kota ' => '',
            'provinsi ' => '',
            'kelurahan ' => '',
            'desa ' => '',
            'kecamatan ' => '',
            'kec. ' => '',
        ];
        $name = str_ireplace(array_keys($replacements), array_values($replacements), $name);

        // Remove symbols
        $name = preg_replace('/[^a-z0-9\s]/', '', $name);

        return trim($name);
    }

    protected function logUnmatched()
    {
        if (empty($this->unmatched)) return;

        $filename = 'logs/apicoid_unmatched_' . now()->format('Ymd_His') . '.log';
        $content = "Unmatched Records Log - " . now()->toDateTimeString() . PHP_EOL . str_repeat('-', 50) . PHP_EOL;

        foreach ($this->unmatched as $item) {
            $content .= "[{$item['level']}] API Name: {$item['api_name']} (Norm: {$item['normalized']}) | Parent Context: " . json_encode($item['constraints']) . PHP_EOL;
        }

        Storage::disk('local')->put($filename, $content);
        $this->command->warn("Some records could not be matched. Log saved to: storage/app/{$filename}");
    }
}
