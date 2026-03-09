<?php

namespace App\Console\Commands;

use App\Models\District;
use App\Models\Province;
use App\Models\SubDistrict;
use App\Models\Village;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportRegionalJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regional:export-json 
                            {--disk= : Specify the storage disk} 
                            {--level=all : Specify level: provinces, districts, sub_districts, villages, or all} 
                            {--chunk=1000 : Specify chunk size for database queries}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export regional data from database to JSON files';

    protected string $disk;
    protected string $basePath;
    protected int $chunkSize;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->disk = $this->option('disk') ?: config('regional.export.disk', 'local');
        $this->basePath = config('regional.export.path', 'locations/indonesia');
        $this->chunkSize = (int) $this->option('chunk') ?: (int) config('regional.export.chunk_size', 1000);
        $level = $this->option('level');

        $this->info("Exporting regional data (Disk: {$this->disk}, Chunk: {$this->chunkSize})...");

        if ($level === 'all' || $level === 'provinces') {
            $this->exportLevel(Province::class, 'provinces.json');
        }

        if ($level === 'all' || $level === 'districts') {
            $this->exportLevel(District::class, 'districts.json');
        }

        if ($level === 'all' || $level === 'sub_districts') {
            $this->exportLevel(SubDistrict::class, 'sub_districts.json');
        }

        if ($level === 'all' || $level === 'villages') {
            $this->exportLevel(Village::class, 'villages.json');
        }

        $this->info('Export completed successfully.');
    }

    protected function exportLevel(string $model, string $filename)
    {
        $this->comment("Exporting {$filename}...");

        $filePath = "{$this->basePath}/{$filename}";

        // Ensure directory exists
        Storage::disk($this->disk)->makeDirectory($this->basePath);

        // We use a file handle to write JSON array to avoid loading everything in memory
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, '[');

        $isFirst = true;

        $model::query()->chunk($this->chunkSize, function ($records) use (&$isFirst, $handle) {
            foreach ($records as $record) {
                if (!$isFirst) {
                    fwrite($handle, ',');
                }
                fwrite($handle, json_encode($record->toArray()));
                $isFirst = false;
            }
        });

        fwrite($handle, ']');
        rewind($handle);

        Storage::disk($this->disk)->put($filePath, stream_get_contents($handle));
        fclose($handle);

        $this->info("  Stored at: {$filePath}");
    }
}
