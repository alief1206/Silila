<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Lp2bStreamingImporter extends BaseSpatialStreamingImporter
{
    /**
     * Tipe layer geometri LP2B (1 = LP2B)
     */
    protected function getLayerType(): int
    {
        return 1;
    }

    /**
     * Memproses satu objek fitur GeoJSON / ESRI JSON LP2B dengan logika Smart Upsert & Point-in-Polygon
     *
     * @param array $feature
     * @return string Status: 'inserted' | 'updated' | 'skipped' | 'failed'
     */
    protected function processFeature(array $feature): string
    {
        // 1. Normalisasi atribut (dukung properties GeoJSON maupun attributes ESRI JSON)
        $properties = $feature['properties'] ?? $feature['attributes'] ?? [];

        // 2. Normalisasi geometry (dukung GeoJSON coordinates maupun ESRI rings)
        $rawGeometry = $feature['geometry'] ?? null;
        if (empty($rawGeometry) && isset($feature['rings'])) {
            $rawGeometry = ['rings' => $feature['rings']];
        }

        if (empty($rawGeometry)) {
            return 'failed';
        }

        $geometry = $this->normalizeGeometryToGeoJson($rawGeometry);
        if (empty($geometry)) {
            return 'failed';
        }

        // 3. Tentukan Identifier Unik GIS
        $objectId = $properties['FID'] 
            ?? $properties['fid'] 
            ?? $properties['OBJECTID'] 
            ?? $properties['OBJECTID_1'] 
            ?? $properties['FID_HASIL_'] 
            ?? $properties['object_id']
            ?? $properties['ID']
            ?? $properties['id']
            ?? null;

        if ($objectId === null || $objectId === '') {
            $objectId = 'GEO_' . substr(md5(json_encode($geometry)), 0, 16);
        }

        // 4. Ambil atribut-atribut spesifik LP2B
        $kp2b = $properties['LP2B'] 
            ?? $properties['lp2b'] 
            ?? $properties['KP2B'] 
            ?? $properties['kp2b'] 
            ?? 'LP2B';

        $ket = $properties['LBS'] 
            ?? $properties['lbs'] 
            ?? $properties['KET'] 
            ?? $properties['ket'] 
            ?? '-';

        $rawLuas = $properties['LUAS_Ha'] 
            ?? $properties['luas_ha'] 
            ?? $properties['Luas_Ha'] 
            ?? $properties['LUAS'] 
            ?? $properties['luas'] 
            ?? 0;

        $luas = is_numeric($rawLuas) ? (float)$rawLuas : 0;

        // Otomatis konversi Hektare (Ha) ke meter persegi (m²), 1 Ha = 10.000 m²
        if (
            isset($properties['LUAS_Ha']) || 
            isset($properties['luas_ha']) || 
            isset($properties['Luas_Ha']) || 
            ($luas > 0 && $luas < 100)
        ) {
            $luas = round($luas * 10000, 2);
        }

        // 5. Penentuan Wilayah Administratif Desa & Kecamatan
        // Jika teks desa tidak ada di atribut, gunakan Point-in-Polygon Engine (batas.json)
        $rawDesa = trim($properties['DESA'] ?? $properties['desa'] ?? '');
        $rawKec  = trim($properties['KECAMATAN'] ?? $properties['kecamatan'] ?? '');

        if (empty($rawDesa) || strtoupper($rawDesa) === 'DESA') {
            $location = $this->resolveLocationFromGeometry($geometry);
            $desaName = $location['desa'];
            $kecName  = $location['kecamatan'];
        } else {
            $desaName = $rawDesa;
            $kecName  = !empty($rawKec) ? $rawKec : 'BANYUWANGI';
        }

        $desaId = $this->resolveDesaId($kecName, $desaName);

        // 6. Siapkan Geometri JSON untuk MySQL Spatial
        $geometryJson   = json_encode($geometry);
        $quotedGeomJson = DB::getPdo()->quote($geometryJson);

        // 7. Cek apakah poligon dengan object_id ini sudah ada di tabel geometri (tipe 1 = LP2B)
        $existingGeometri = DB::table('geometri')
            ->where('tipe', 1)
            ->where('object_id', (string)$objectId)
            ->first();

        if ($existingGeometri) {
            $existingLp2b = DB::table('data_lp2b')
                ->where('geometri_id', $existingGeometri->id)
                ->first();

            if ($existingLp2b) {
                // DIFF CHECK: Jika semua atribut sama dan desa_id sama, lewati (hemat query)
                if (
                    $existingLp2b->kp2b === (string)$kp2b &&
                    $existingLp2b->ket === (string)$ket &&
                    abs((float)$existingLp2b->luas - (float)$luas) < 0.01 &&
                    $existingGeometri->desa_id === $desaId
                ) {
                    return 'skipped';
                }

                // Jika ada perubahan, lakukan UPDATE
                try {
                    DB::table('geometri')->where('id', $existingGeometri->id)->update([
                        'desa_id'    => $desaId,
                        'koordinat'  => DB::raw("ST_GeomFromGeoJSON({$quotedGeomJson})"),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    Log::warning("Gagal update geometri LP2B ID {$existingGeometri->id}: " . $e->getMessage());
                }

                DB::table('data_lp2b')->where('id', $existingLp2b->id)->update([
                    'kp2b'       => $kp2b,
                    'ket'        => $ket,
                    'luas'       => $luas,
                    'updated_at' => now(),
                ]);

                return 'updated';
            }
        }

        // 8. Jika belum ada, lakukan INSERT baru
        try {
            $geomId = DB::table('geometri')->insertGetId([
                'object_id'  => (string)$objectId,
                'desa_id'    => $desaId,
                'tipe'       => 1, // 1: LP2B
                'koordinat'  => DB::raw("ST_GeomFromGeoJSON({$quotedGeomJson})"),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("ST_GeomFromGeoJSON gagal untuk object_id LP2B {$objectId}: " . $e->getMessage());
            return 'failed';
        }

        DB::table('data_lp2b')->insert([
            'geometri_id' => $geomId,
            'kp2b'        => $kp2b,
            'ket'         => $ket,
            'luas'        => $luas,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return 'inserted';
    }
}
