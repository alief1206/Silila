<?php

namespace App\Console\Commands;

use App\Services\LsdStreamingImporter;
use Illuminate\Console\Command;

class ImportLsdSpatialCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spatial:import-lsd 
                            {file? : Path absolut atau relatif ke file GeoJSON LSD}
                            {--chunk=100 : Ukuran batch commit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Impor data spasial LSD (200MB GeoJSON) secara streaming dengan Smart Upsert & hemat memori';

    /**
     * Execute the console command.
     */
    public function handle(LsdStreamingImporter $importer)
    {
        $filePath = $this->argument('file');

        // Jika tidak diisi, cari default di storage/app/spatial atau uploads
        if (!$filePath) {
            $defaultPaths = [
                storage_path('app/spatial/lsd.geojson'),
                storage_path('app/lsd.geojson'),
                public_path('uploads/lsd.geojson'),
            ];

            foreach ($defaultPaths as $path) {
                if (file_exists($path)) {
                    $filePath = $path;
                    break;
                }
            }
        }

        if (!$filePath || !file_exists($filePath)) {
            $this->error('File GeoJSON LSD tidak ditemukan!');
            $this->line("Silakan masukkan path yang valid, contoh:");
            $this->line("  php artisan spatial:import-lsd \"storage/app/spatial/lsd.geojson\"");
            return Command::FAILURE;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $tempExtracted = null;

        if ($ext === 'zip' && class_exists('\ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entryName = $zip->getNameIndex($i);
                    $entryExt = strtolower(pathinfo($entryName, PATHINFO_EXTENSION));
                    if (in_array($entryExt, ['json', 'geojson'])) {
                        $tempExtracted = tempnam(sys_get_temp_dir(), 'zip_cli_lsd_') . '.' . $entryExt;
                        $zipStream = $zip->getStream($entryName);
                        if ($zipStream) {
                            $outStream = fopen($tempExtracted, 'wb');
                            stream_copy_to_stream($zipStream, $outStream);
                            fclose($outStream);
                            fclose($zipStream);
                        }
                        $filePath = $tempExtracted;
                        break;
                    }
                }
                $zip->close();
            }
        }

        $fileSizeBytes = filesize($filePath);
        $fileSizeMb = round($fileSizeBytes / 1024 / 1024, 2);

        $this->info("===============================================================");
        $this->info("🚀 MEMULAI STREAMING IMPOR DATA SPASIAL LSD (SiLiLa)");
        $this->info("===============================================================");
        $this->line("📁 Berkas : {$filePath} ({$fileSizeMb} MB)");
        $this->line("⚡ Metode : Stream Processing (Hemat Memori RAM) + Smart Upsert");
        $this->newLine();

        $lastScanned = 0;

        try {
            $stats = $importer->import($filePath, function (array $currentStats) use (&$lastScanned) {
                $scanned = $currentStats['total_scanned'];
                $inserted = $currentStats['inserted'];
                $updated = $currentStats['updated'];
                $skipped = $currentStats['skipped'];
                $failed = $currentStats['failed'];
                $mem = $currentStats['memory_peak'];

                $this->output->write(
                    "\r📊 Dipindai: <info>{$scanned}</info> bidang | Baru: <fg=green>{$inserted}</> | Diperbarui: <fg=yellow>{$updated}</> | Dilewati: <fg=blue>{$skipped}</> | RAM: {$mem} MB"
                );
            });

            $this->newLine(2);
            $this->info("===============================================================");
            $this->info("✅ PROSES IMPOR SELESAI!");
            $this->info("===============================================================");

            $this->table(
                ['Metrik', 'Nilai'],
                [
                    ['Total Poligon Dipindai', number_format($stats['total_scanned'])],
                    ['Data Baru Ditambahkan (Insert)', number_format($stats['inserted'])],
                    ['Data Diperbarui (Update)', number_format($stats['updated'])],
                    ['Data Tidak Berubah (Skipped)', number_format($stats['skipped'])],
                    ['Data Gagal/Invalid', number_format($stats['failed'])],
                    ['Waktu Pemrosesan', $stats['duration_seconds'] . ' detik'],
                    ['Puncak Penggunaan Memori RAM', $stats['memory_peak'] . ' MB'],
                ]
            );

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error("Terjadi kesalahan saat impor: " . $e->getMessage());
            $this->line($e->getTraceAsString());
            return Command::FAILURE;
        } finally {
            if ($tempExtracted && file_exists($tempExtracted)) {
                @unlink($tempExtracted);
            }
        }
    }
}
