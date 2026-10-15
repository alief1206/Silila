<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LsdStreamingImporter extends BaseSpatialStreamingImporter
{
    /**
     * Tipe layer geometri LSD (2 = LSD)
     */
    protected function getLayerType(): int
    {
        return 2;
    }

    /**
     * Memproses satu objek fitur GeoJSON / ESRI JSON dengan logika Smart Upsert LSD
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

        // Tentukan Identifier Unik GIS
        $objectId = $properties['OBJECTID'] 
            ?? $properties['OBJECTID_1'] 
            ?? $properties['FID'] 
            ?? $properties['fid'] 
            ?? $properties['FID_HASIL_'] 
            ?? $properties['object_id']
            ?? $properties['ID']
            ?? $properties['id']
            ?? null;

        if (empty($objectId)) {
            $objectId = 'GEO_' . substr(md5(json_encode($geometry)), 0, 16);
        }

        // Ambil atribut-atribut penting (mendukung format standar BPN maupun ESRI)
        $statusLsd  = $properties['LSD'] ?? $properties['lsd'] ?? $properties['LBS'] ?? $properties['lbs'] ?? $properties['LP2B'] ?? $properties['lp2b'] ?? null;
        $hutan      = $properties['HUTAN'] ?? $properties['hutan'] ?? '-';
        $rawLuas    = $properties['LUAS'] ?? $properties['luas'] ?? $properties['LUAS_Ha'] ?? $properties['luas_ha'] ?? $properties['Luas_Ha'] ?? 0;
        $luas       = is_numeric($rawLuas) ? (float)$rawLuas : 0;
        // Seluruh satuan dikonversi ke meter persegi (m²), 1 Ha = 10.000 m²
        if (
            isset($properties['LUAS_Ha']) || 
            isset($properties['luas_ha']) || 
            isset($properties['Luas_Ha']) || 
            isset($properties['LuasCEA_HM']) || 
            isset($properties['luascea_hm']) || 
            ($luas > 0 && $luas < 100)
        ) {
            $luas = round($luas * 10000, 2);
        }
        $ba         = $properties['BA'] ?? $properties['ba'] ?? '-';
        $luasceaHm  = $properties['LuasCEA_HM'] ?? $properties['luascea_hm'] ?? $properties['LUASCEA_HM'] ?? null;
        $irigasi    = $properties['IRIGASI'] ?? $properties['irigasi'] ?? null;
        $kewenangan = $properties['Kewenangan'] ?? $properties['kewenangan'] ?? null;
        $polru      = $properties['Polru'] ?? $properties['polru'] ?? null;
        $ip         = $properties['IP'] ?? $properties['ip'] ?? null;
        $prod       = $properties['PROD'] ?? $properties['prod'] ?? null;
        $tipeHak    = $properties['TIPEHAK'] ?? $properties['tipehak'] ?? null;
        $investasi  = $properties['INVESTASI'] ?? $properties['investasi'] ?? null;

        // Wilayah administratif
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

        // Hitung hash dari properties untuk deteksi perubahan cepat (diff check)
        $propertiesJson = json_encode($properties);
        $propertiesHash = md5($propertiesJson);
        $geometryJson   = json_encode($geometry);
        $quotedGeomJson = DB::getPdo()->quote($geometryJson);

        // 1. Cek apakah poligon dengan object_id ini sudah ada di tabel geometri (tipe 2 = LSD)
        $existingGeometri = null;
        if (!empty($objectId)) {
            $existingGeometri = DB::table('geometri')
                ->where('tipe', 2)
                ->where('object_id', (string)$objectId)
                ->first();
        }

        if ($existingGeometri) {
            // Data geometri sudah ada, cek tabel data_lsd
            $existingLsd = DB::table('data_lsd')
                ->where('geometri_id', $existingGeometri->id)
                ->first();

            if ($existingLsd) {
                // DIFF CHECK: Jika hash identik dan desa sama, SKIP hemat query!
                if ($existingLsd->properties_hash === $propertiesHash && $existingGeometri->desa_id === $desaId) {
                    return 'skipped';
                }

                // Jika hash berbeda, lakukan UPDATE
                try {
                    DB::table('geometri')->where('id', $existingGeometri->id)->update([
                        'desa_id'    => $desaId,
                        'koordinat'  => DB::raw("ST_GeomFromGeoJSON({$quotedGeomJson})"),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    Log::warning("Gagal update geometri LSD ID {$existingGeometri->id}: " . $e->getMessage());
                }

                DB::table('data_lsd')->where('id', $existingLsd->id)->update([
                    'status_lsd'      => $statusLsd,
                    'hutan'           => $hutan,
                    'luas'            => $luas,
                    'irigasi'         => $irigasi,
                    'kewenangan'      => $kewenangan,
                    'polru'           => $polru,
                    'ip'              => $ip,
                    'prod'            => $prod,
                    'tipehak'         => $tipeHak,
                    'investasi'       => $investasi,
                    'properties'      => $propertiesJson,
                    'properties_hash' => $propertiesHash,
                    'ba'              => $ba,
                    'luascea_hm'      => $luasceaHm,
                    'updated_at'      => now(),
                ]);

                return 'updated';
            }
        }

        // 2. Jika belum ada, lakukan INSERT baru
        try {
            $geomId = DB::table('geometri')->insertGetId([
                'object_id'  => !empty($objectId) ? (string)$objectId : null,
                'desa_id'    => $desaId,
                'tipe'       => 2, // 2: LSD
                'koordinat'  => DB::raw("ST_GeomFromGeoJSON({$quotedGeomJson})"),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("ST_GeomFromGeoJSON gagal untuk object_id {$objectId}: " . $e->getMessage());
            return 'failed';
        }

        DB::table('data_lsd')->insert([
            'geometri_id'     => $geomId,
            'status_lsd'      => $statusLsd,
            'hutan'           => $hutan,
            'luas'            => $luas,
            'irigasi'         => $irigasi,
            'kewenangan'      => $kewenangan,
            'polru'           => $polru,
            'ip'              => $ip,
            'prod'            => $prod,
            'tipehak'         => $tipeHak,
            'investasi'       => $investasi,
            'properties'      => $propertiesJson,
            'properties_hash' => $propertiesHash,
            'ba'              => $ba,
            'luascea_hm'      => $luasceaHm,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return 'inserted';
    }
}
