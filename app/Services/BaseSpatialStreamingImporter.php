<?php

namespace App\Services;

use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class BaseSpatialStreamingImporter
{
    /**
     * Cache in-memory untuk Kecamatan dan Desa agar query DB cepat
     */
    protected array $kecamatanCache = [];
    protected array $desaCache = [];

    /**
     * Cache statis untuk indeks batas wilayah desa Banyuwangi dari batas.json
     */
    protected static ?array $boundaryIndex = null;

    /**
     * Tipe layer geometri (1: LP2B, 2: LSD, 3: LBS)
     */
    abstract protected function getLayerType(): int;

    /**
     * Pemrosesan spesifik satu fitur spasial (harus diimplementasikan di kelas turunan)
     *
     * @param array $feature
     * @return string Status hasil: 'inserted' | 'updated' | 'skipped' | 'failed'
     */
    abstract protected function processFeature(array $feature): string;

    /**
     * Driver loop impor streaming per fitur tanpa membebani RAM
     *
     * @param string $filePath
     * @param callable|null $progressCallback
     * @return array
     */
    public function import(string $filePath, ?callable $progressCallback = null): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File tidak ditemukan: {$filePath}");
        }

        $startTime = microtime(true);
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Gagal membuka file: {$filePath}");
        }

        $stats = [
            'total_scanned' => 0,
            'inserted'      => 0,
            'updated'       => 0,
            'skipped'       => 0,
            'failed'        => 0,
            'start_time'    => $startTime,
            'memory_peak'   => 0,
        ];

        DB::beginTransaction();

        try {
            $featureGenerator = $this->streamFeatures($handle);

            foreach ($featureGenerator as $feature) {
                $stats['total_scanned']++;

                try {
                    $result = $this->processFeature($feature);
                    if ($result === 'inserted') {
                        $stats['inserted']++;
                    } elseif ($result === 'updated') {
                        $stats['updated']++;
                    } elseif ($result === 'skipped') {
                        $stats['skipped']++;
                    } else {
                        $stats['failed']++;
                    }
                } catch (\Throwable $e) {
                    $stats['failed']++;
                    Log::warning("Gagal memproses fitur layer {$this->getLayerType()} pada indeks {$stats['total_scanned']}: " . $e->getMessage());
                }

                // Batch commit setiap 100 fitur agar memori dan database stabil
                if ($stats['total_scanned'] % 100 === 0) {
                    DB::commit();
                    DB::beginTransaction();

                    if (function_exists('gc_collect_cycles')) {
                        gc_collect_cycles();
                    }

                    if ($progressCallback) {
                        $stats['memory_peak'] = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
                        $progressCallback($stats);
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }

        fclose($handle);

        $endTime = microtime(true);
        $stats['duration_seconds'] = round($endTime - $startTime, 2);
        $stats['memory_peak'] = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        return $stats;
    }

    /**
     * Memuat dan mengindeks poligon desa Banyuwangi dari public/assets/batas/batas.json
     * Menggunakan Bounding Box (BBox) pre-filtering agar pencarian berkecepatan mikrodetik.
     */
    protected function getBoundaryIndex(): array
    {
        if (self::$boundaryIndex !== null) {
            return self::$boundaryIndex;
        }

        $batasPath = public_path('assets/batas/batas.json');
        if (!file_exists($batasPath)) {
            Log::warning("File batas.json tidak ditemukan pada path: {$batasPath}");
            self::$boundaryIndex = [];
            return self::$boundaryIndex;
        }

        $json = @file_get_contents($batasPath);
        $decoded = @json_decode($json, true);
        $features = $decoded['features'] ?? [];

        $index = [];
        foreach ($features as $f) {
            $p = $f['properties'] ?? [];
            $geom = $f['geometry'] ?? [];
            $desa = trim($p['DESA'] ?? '');
            $kec = trim($p['KECAMATAN'] ?? '');

            if (empty($desa)) continue;

            $minX = 180.0; $maxX = -180.0;
            $minY = 90.0;  $maxY = -90.0;
            $ringsList = [];

            $type = $geom['type'] ?? '';
            $coords = $geom['coordinates'] ?? [];
            if ($type === 'Polygon') {
                $ringsList = $coords;
            } elseif ($type === 'MultiPolygon') {
                foreach ($coords as $poly) {
                    if (is_array($poly)) {
                        foreach ($poly as $ring) {
                            if (is_array($ring)) {
                                $ringsList[] = $ring;
                            }
                        }
                    }
                }
            }

            foreach ($ringsList as $ring) {
                foreach ($ring as $pt) {
                    if (isset($pt[0], $pt[1]) && is_numeric($pt[0]) && is_numeric($pt[1])) {
                        if ($pt[0] < $minX) $minX = (float)$pt[0];
                        if ($pt[0] > $maxX) $maxX = (float)$pt[0];
                        if ($pt[1] < $minY) $minY = (float)$pt[1];
                        if ($pt[1] > $maxY) $maxY = (float)$pt[1];
                    }
                }
            }

            $index[] = [
                'desa'      => $desa,
                'kecamatan' => $kec,
                'bbox'      => [$minX, $minY, $maxX, $maxY],
                'center'    => [($minX + $maxX) / 2, ($minY + $maxY) / 2],
                'rings'     => $ringsList,
            ];
        }

        self::$boundaryIndex = $index;
        return self::$boundaryIndex;
    }

    /**
     * Menentukan nama Desa dan Kecamatan secara spasial dari koordinat poligon
     * Menggunakan algoritma Point-in-Polygon (Ray-Casting) yang dioptimasi BBox.
     */
    protected function resolveLocationFromGeometry(array $geometry): array
    {
        $boundaryIndex = $this->getBoundaryIndex();
        if (empty($boundaryIndex)) {
            return ['desa' => 'DESA', 'kecamatan' => 'BANYUWANGI'];
        }

        $centroid = $this->calculateCentroid($geometry);
        if (!$centroid) {
            return ['desa' => 'DESA', 'kecamatan' => 'BANYUWANGI'];
        }

        $lng = $centroid[0];
        $lat = $centroid[1];

        // 1. Uji Point-in-Polygon dengan BBox pre-filter
        foreach ($boundaryIndex as $item) {
            list($minX, $minY, $maxX, $maxY) = $item['bbox'];
            if ($lng >= $minX && $lng <= $maxX && $lat >= $minY && $lat <= $maxY) {
                foreach ($item['rings'] as $ring) {
                    if ($this->pointInPolygon($lng, $lat, $ring)) {
                        return [
                            'desa'      => $item['desa'],
                            'kecamatan' => !empty($item['kecamatan']) ? $item['kecamatan'] : 'BANYUWANGI',
                        ];
                    }
                }
            }
        }

        // 2. Fallback: Jika di luar garis poligon (garis pantai/perbatasan), pilih desa dengan jarak BBox terdekat
        $minDist = PHP_FLOAT_MAX;
        $closest = null;
        foreach ($boundaryIndex as $item) {
            $cx = $item['center'][0];
            $cy = $item['center'][1];
            $dist = ($lng - $cx) * ($lng - $cx) + ($lat - $cy) * ($lat - $cy);
            if ($dist < $minDist) {
                $minDist = $dist;
                $closest = $item;
            }
        }

        if ($closest) {
            return [
                'desa'      => $closest['desa'],
                'kecamatan' => !empty($closest['kecamatan']) ? $closest['kecamatan'] : 'BANYUWANGI',
            ];
        }

        return ['desa' => 'DESA', 'kecamatan' => 'BANYUWANGI'];
    }

    /**
     * Hitung centroid (titik tengah representatif) dari ring luar geometri
     */
    protected function calculateCentroid(array $geometry): ?array
    {
        $type = strtolower($geometry['type'] ?? '');
        $coords = $geometry['coordinates'] ?? null;
        if (empty($coords) || !is_array($coords)) {
            return null;
        }

        $ring = [];
        if ($type === 'polygon' && isset($coords[0]) && is_array($coords[0])) {
            $ring = $coords[0];
        } elseif ($type === 'multipolygon' && isset($coords[0][0]) && is_array($coords[0][0])) {
            $ring = $coords[0][0];
        } elseif ($type === 'point' && count($coords) >= 2) {
            return [(float)$coords[0], (float)$coords[1]];
        }

        if (empty($ring)) {
            return null;
        }

        $sumX = 0;
        $sumY = 0;
        $count = 0;
        foreach ($ring as $pt) {
            if (isset($pt[0], $pt[1]) && is_numeric($pt[0]) && is_numeric($pt[1])) {
                $sumX += (float)$pt[0];
                $sumY += (float)$pt[1];
                $count++;
            }
        }

        if ($count === 0) {
            return null;
        }

        return [$sumX / $count, $sumY / $count];
    }

    /**
     * Algoritma Ray-Casting Point-in-Polygon (kebal error topologi)
     */
    protected function pointInPolygon(float $x, float $y, array $ring): bool
    {
        $inside = false;
        $count = count($ring);
        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = $ring[$i][0];
            $yi = $ring[$i][1];
            $xj = $ring[$j][0];
            $yj = $ring[$j][1];

            $intersect = (($yi > $y) !== ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect) {
                $inside = !$inside;
            }
        }
        return $inside;
    }

    /**
     * Resolusi ID Desa & Kecamatan dengan in-memory cache
     */
    protected function resolveDesaId(string $kecName, string $desaName): int
    {
        $kecKey = strtoupper(trim($kecName));
        $desaKey = $kecKey . '|' . strtoupper(trim($desaName));

        if (isset($this->desaCache[$desaKey])) {
            return $this->desaCache[$desaKey];
        }

        if (!isset($this->kecamatanCache[$kecKey])) {
            $kec = Kecamatan::firstOrCreate(['nama' => $kecName]);
            $this->kecamatanCache[$kecKey] = $kec->id;
        }

        $kecId = $this->kecamatanCache[$kecKey];

        $desa = Desa::firstOrCreate([
            'kecamatan_id' => $kecId,
            'nama'         => $desaName,
        ]);

        $this->desaCache[$desaKey] = $desa->id;

        return $desa->id;
    }

    /**
     * Normalisasi struktur geometri (GeoJSON Polygon/MultiPolygon atau ESRI JSON rings)
     * dan pastikan koordinat terproyeksi ke WGS84 Lat/Lng.
     */
    protected function normalizeGeometryToGeoJson($rawGeometry): ?array
    {
        if (!is_array($rawGeometry)) {
            return null;
        }

        $type = $rawGeometry['type'] ?? null;
        $coordinates = $rawGeometry['coordinates'] ?? null;

        // 1. Format ESRI JSON dengan "rings"
        if (isset($rawGeometry['rings']) && is_array($rawGeometry['rings'])) {
            $rings = $this->normalizeCoordinatesRecursively($rawGeometry['rings']);
            $rings = $this->ensureRingsClosed($rings);
            return [
                'type'        => 'Polygon',
                'coordinates' => $rings,
            ];
        }

        // 2. Format standar GeoJSON
        if (!empty($type) && !empty($coordinates) && is_array($coordinates)) {
            $coordinates = $this->normalizeCoordinatesRecursively($coordinates);
            if (in_array(strtolower($type), ['polygon', 'multipolygon'])) {
                $coordinates = $this->ensureRingsClosed($coordinates);
            }
            return [
                'type'        => ucfirst($type),
                'coordinates' => $coordinates,
            ];
        }

        return null;
    }

    /**
     * Normalisasi koordinat secara rekursif.
     * Jika koordinat adalah projected meters (misal WKID 54034 / Web Mercator),
     * otomatis reproject ke derajat WGS84 (Lng, Lat).
     */
    protected function normalizeCoordinatesRecursively(array $coords): array
    {
        if (isset($coords[0]) && is_numeric($coords[0]) && isset($coords[1]) && is_numeric($coords[1])) {
            $x = (float)$coords[0];
            $y = (float)$coords[1];

            // Jika abs(x) > 180 atau abs(y) > 90, ini merupakan proyeksi meter (WKID 54034 / Cylindrical Equal Area)
            if (abs($x) > 180 || abs($y) > 90) {
                $a = 6378137.0; // WGS84 semi-major axis
                $lng = ($x / $a) * (180 / M_PI);
                $sinLat = $y / $a;
                if ($sinLat > 1)  $sinLat = 1;
                if ($sinLat < -1) $sinLat = -1;
                $lat = asin($sinLat) * (180 / M_PI);

                return [round($lng, 7), round($lat, 7)];
            }

            return [$x, $y];
        }

        foreach ($coords as $k => $v) {
            if (is_array($v)) {
                $coords[$k] = $this->normalizeCoordinatesRecursively($v);
            }
        }

        return $coords;
    }

    /**
     * Memastikan ring poligon tertutup rapat (first point == last point)
     */
    protected function ensureRingsClosed(array $rings): array
    {
        foreach ($rings as $k => $ring) {
            if (is_array($ring)) {
                if (isset($ring[0]) && is_array($ring[0]) && isset($ring[0][0]) && is_numeric($ring[0][0])) {
                    $count = count($ring);
                    if ($count > 0) {
                        $first = $ring[0];
                        $last  = $ring[$count - 1];
                        if ($first[0] !== $last[0] || $first[1] !== $last[1]) {
                            $ring[] = $first;
                        }
                    }
                    $rings[$k] = $ring;
                } else {
                    $rings[$k] = $this->ensureRingsClosed($ring);
                }
            }
        }
        return $rings;
    }

    /**
     * Generator streaming yang membaca file GeoJSON / ESRI JSON buffer demi buffer
     * dan mengekstrak objek Feature bergiliran tanpa lonjakan RAM.
     */
    protected function streamFeatures($handle): \Generator
    {
        $inFeaturesArray = false;
        $braceDepth = 0;
        $inString = false;
        $isEscaped = false;
        $buffer = '';

        while (!feof($handle)) {
            $chunk = fread($handle, 65536); // Baca potongan 64 KB
            $len = strlen($chunk);

            for ($i = 0; $i < $len; $i++) {
                $char = $chunk[$i];

                if (!$inFeaturesArray) {
                    $buffer .= $char;
                    if (stripos($buffer, '"features"') !== false) {
                        if ($char === '[') {
                            $inFeaturesArray = true;
                            $buffer = '';
                        }
                    } elseif ($char === '[' && trim($buffer) === '[') {
                        $inFeaturesArray = true;
                        $buffer = '';
                    } elseif (strlen($buffer) > 2000) {
                        $buffer = substr($buffer, -500);
                    }
                    continue;
                }

                if ($braceDepth === 0) {
                    if ($char === '{') {
                        $braceDepth = 1;
                        $buffer = '{';
                        $inString = false;
                        $isEscaped = false;
                    } elseif ($char === ']') {
                        return;
                    }
                    continue;
                }

                $buffer .= $char;

                if ($inString) {
                    if ($char === '\\') {
                        $isEscaped = !$isEscaped;
                    } elseif ($char === '"' && !$isEscaped) {
                        $inString = false;
                    } else {
                        $isEscaped = false;
                    }
                } else {
                    if ($char === '"') {
                        $inString = true;
                        $isEscaped = false;
                    } elseif ($char === '{') {
                        $braceDepth++;
                    } elseif ($char === '}') {
                        $braceDepth--;
                        if ($braceDepth === 0) {
                            $decoded = json_decode($buffer, true);
                            if (is_array($decoded)) {
                                if (
                                    (isset($decoded['type']) && strtolower($decoded['type']) === 'feature') ||
                                    isset($decoded['geometry']) ||
                                    isset($decoded['attributes']) ||
                                    isset($decoded['properties']) ||
                                    isset($decoded['rings'])
                                ) {
                                    yield $decoded;
                                }
                            }
                            $buffer = '';
                        }
                    }
                }
            }
        }
    }
}
