<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpatialImportController extends Controller
{
    public function importLP2B(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // Maksimal 50MB
        ]);

        try {
            $features = $this->extractFeaturesFromFile($request->file('file'));

            if (empty($features)) {
                return back()->with('error', 'Format atau isi file spasial LP2B tidak valid / kosong.');
            }

            // Pecah data menjadi beberapa bagian (500 baris per query) agar ringan di MySQL
            $chunks = array_chunk($features, 500);

            DB::beginTransaction();
            foreach ($chunks as $chunk) {
                foreach ($chunk as $feature) {
                    $properties = $feature['properties'] ?? [];

                    // Validasi: Data harus memiliki koordinat (geometry) atau geometri_id yang sudah ada
                    $geometry = $feature['geometry'] ?? null;
                    $geomIdProp = $properties['geometri_id'] ?? $properties['GEOMETRI_ID'] ?? $properties['Geometri_ID'] ?? null;
                    if (empty($geometry)) {
                        if (empty($geomIdProp) || !DB::table('geometri')->where('id', $geomIdProp)->exists()) {
                            DB::rollBack();
                            return back()->with('error', 'Validasi gagal: Terdapat data yang tidak memiliki titik koordinat (geometry) dan tidak tertaut dengan ID Geometri yang valid.');
                        }
                    }

                    $geometriId = $this->ensureGeometriRecord($feature, 1);

                    $kp2b = $properties['kp2b'] ?? $properties['KP2B'] ?? $properties['Kp2b'] ?? null;
                    $ket  = $properties['ket'] ?? $properties['KET'] ?? $properties['Ket'] ?? null;
                    $luas = $properties['luas'] ?? $properties['LUAS'] ?? $properties['Luas'] ?? null;

                    $existing = DB::table('data_lp2b')->where('geometri_id', $geometriId)->first();
                    
                    if ($existing) {
                        // Jika sudah ada, cek apakah ada perubahan
                        if ($existing->kp2b != $kp2b || $existing->ket != $ket || $existing->luas != $luas) {
                            DB::table('data_lp2b')->where('id', $existing->id)->update([
                                'kp2b'       => $kp2b,
                                'ket'        => $ket,
                                'luas'       => $luas,
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        // Jika belum ada, masukkan data baru
                        DB::table('data_lp2b')->insert([
                            'geometri_id' => $geometriId,
                            'kp2b'        => $kp2b,
                            'ket'         => $ket,
                            'luas'        => $luas,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);
                    }
                }
            }
            DB::commit();

            return back()->with('success', 'Data LP2B berhasil diimpor secara massal!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal import LP2B: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat impor LP2B.');
        }
    }

    public function importLSD(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200',
        ]);

        try {
            $features = $this->extractFeaturesFromFile($request->file('file'));

            if (empty($features)) {
                return back()->with('error', 'Format atau isi file spasial LSD tidak valid / kosong.');
            }

            // Pecah data per 500 baris
            $chunks = array_chunk($features, 500);

            DB::beginTransaction();
            foreach ($chunks as $chunk) {
                foreach ($chunk as $feature) {
                    $p = $feature['properties'] ?? [];

                    // Validasi: Data harus memiliki koordinat (geometry) atau geometri_id yang sudah ada
                    $geometry = $feature['geometry'] ?? null;
                    $geomIdProp = $p['geometri_id'] ?? $p['GEOMETRI_ID'] ?? $p['Geometri_ID'] ?? null;
                    if (empty($geometry)) {
                        if (empty($geomIdProp) || !DB::table('geometri')->where('id', $geomIdProp)->exists()) {
                            DB::rollBack();
                            return back()->with('error', 'Validasi gagal: Terdapat data LSD yang tidak memiliki titik koordinat (geometry) dan tidak tertaut dengan ID Geometri yang valid.');
                        }
                    }

                    $geometriId = $this->ensureGeometriRecord($feature, 2);

                    $hutan      = $p['hutan'] ?? $p['HUTAN'] ?? null;
                    $luas       = $p['luas'] ?? $p['LUAS'] ?? null;
                    $ba         = $p['ba'] ?? $p['BA'] ?? null;
                    $luascea_hm = $p['luascea_hm'] ?? $p['LUASCEA_HM'] ?? null;

                    $existing = DB::table('data_lsd')->where('geometri_id', $geometriId)->first();

                    if ($existing) {
                        // Jika sudah ada, cek apakah ada perubahan
                        if ($existing->hutan != $hutan || $existing->luas != $luas || $existing->ba != $ba || $existing->luascea_hm != $luascea_hm) {
                            DB::table('data_lsd')->where('id', $existing->id)->update([
                                'hutan'      => $hutan,
                                'luas'       => $luas,
                                'ba'         => $ba,
                                'luascea_hm' => $luascea_hm,
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        // Jika belum ada, masukkan data baru
                        DB::table('data_lsd')->insert([
                            'geometri_id' => $geometriId,
                            'hutan'       => $hutan,
                            'luas'        => $luas,
                            'ba'          => $ba,
                            'luascea_hm'  => $luascea_hm,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);
                    }
                }
            }
            DB::commit();

            return back()->with('success', 'Data LSD berhasil diimpor secara massal!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal import LSD: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat impor LSD.');
        }
    }

    /**
     * Memastikan ID geometri tersedia di tabel geometri
     */
    private function ensureGeometriRecord($feature, $tipe = 1)
    {
        $properties = $feature['properties'] ?? [];
        $geometriId = $properties['geometri_id'] ?? $properties['GEOMETRI_ID'] ?? $properties['Geometri_ID'] ?? null;

        if ($geometriId && DB::table('geometri')->where('id', $geometriId)->exists()) {
            return $geometriId;
        }

        $kecName = $properties['KECAMATAN'] ?? $properties['kecamatan'] ?? 'BANYUWANGI';
        $desaName = $properties['DESA'] ?? $properties['desa'] ?? 'DESA';

        $kec = \App\Models\Kecamatan::firstOrCreate(['nama' => $kecName]);
        $desa = \App\Models\Desa::firstOrCreate([
            'kecamatan_id' => $kec->id,
            'nama' => $desaName
        ]);

        $geometry = $feature['geometry'] ?? null;
        $insertGeom = [
            'desa_id'    => $desa->id,
            'tipe'       => $tipe,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($geometry) {
            $insertGeom['koordinat'] = DB::raw("ST_GeomFromGeoJSON('" . json_encode($geometry) . "')");
        }

        return DB::table('geometri')->insertGetId($insertGeom);
    }

    /**
     * Ekstraksi array features dari berbagai format file spasial (GeoJSON, DBF, Shapefile, ZIP, RAR)
     */
    private function extractFeaturesFromFile($file)
    {
        $path = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());
        $features = [];

        if (in_array($extension, ['json', 'geojson', 'txt'])) {
            $content = file_get_contents($path);
            $data = json_decode($content, true);
            $features = $data['features'] ?? [];
        } elseif ($extension === 'zip') {
            $features = $this->parseZipFile($path);
        } elseif ($extension === 'dbf') {
            $features = $this->parseDbfFile($path);
        } elseif ($extension === 'shp') {
            $dbfPath = preg_replace('/\.shp$/i', '.dbf', $path);
            if (file_exists($dbfPath)) {
                $features = $this->parseDbfFile($dbfPath);
            }
        } elseif ($extension === 'rar' && class_exists('\RarArchive')) {
            $rar = \RarArchive::open($path);
            if ($rar !== false) {
                foreach ($rar->getEntries() as $entry) {
                    $eExt = strtolower(pathinfo($entry->getName(), PATHINFO_EXTENSION));
                    if (in_array($eExt, ['json', 'geojson'])) {
                        $stream = $entry->getStream();
                        if ($stream) {
                            $content = stream_get_contents($stream);
                            fclose($stream);
                            $data = json_decode($content, true);
                            $features = $data['features'] ?? [];
                            break;
                        }
                    } elseif ($eExt === 'dbf') {
                        $stream = $entry->getStream();
                        if ($stream) {
                            $tmpDbf = tempnam(sys_get_temp_dir(), 'dbfrar_');
                            file_put_contents($tmpDbf, stream_get_contents($stream));
                            fclose($stream);
                            $features = $this->parseDbfFile($tmpDbf);
                            @unlink($tmpDbf);
                            break;
                        }
                    }
                }
                $rar->close();
            }
        }

        return $features;
    }

    /**
     * Membaca atribut file DBF (dBase / Shapefile Table) secara native PHP
     */
    private function parseDbfFile($filePath)
    {
        $handle = @fopen($filePath, 'rb');
        if (!$handle) {
            return [];
        }

        $header = fread($handle, 32);
        if (strlen($header) < 32) {
            fclose($handle);
            return [];
        }

        $unpackHeader = unpack('Vrecords/vheaderLength/vrecordLength', substr($header, 4, 8));
        $numRecords = $unpackHeader['records'] ?? 0;
        $headerLength = $unpackHeader['headerLength'] ?? 0;
        $recordLength = $unpackHeader['recordLength'] ?? 0;

        if ($headerLength <= 0 || $recordLength <= 0) {
            fclose($handle);
            return [];
        }

        $fields = [];
        while (ftell($handle) < $headerLength - 1) {
            $fieldHeader = fread($handle, 32);
            if (strlen($fieldHeader) < 32 || ord($fieldHeader[0]) == 0x0D) {
                break;
            }
            $fieldName = trim(substr($fieldHeader, 0, 11));
            $fieldName = preg_replace('/[^\x20-\x7E]/', '', $fieldName);
            $fieldLen = ord($fieldHeader[16]);
            if ($fieldName !== '' && $fieldLen > 0) {
                $fields[] = [
                    'name' => $fieldName,
                    'len'  => $fieldLen,
                ];
            }
        }

        fseek($handle, $headerLength);
        $features = [];

        for ($i = 0; $i < $numRecords; $i++) {
            $recordData = fread($handle, $recordLength);
            if (strlen($recordData) < $recordLength) {
                break;
            }
            // Record terhapus jika diawali '*'
            if ($recordData[0] === '*') {
                continue;
            }

            $offset = 1;
            $properties = [];
            foreach ($fields as $field) {
                $value = substr($recordData, $offset, $field['len']);
                $properties[$field['name']] = trim($value);
                $offset += $field['len'];
            }
            $features[] = ['properties' => $properties];
        }

        fclose($handle);
        return $features;
    }

    /**
     * Membaca file ZIP yang berisi GeoJSON atau DBF
     */
    private function parseZipFile($filePath)
    {
        if (!class_exists('\ZipArchive')) {
            return [];
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        $features = [];
        $geojsonContent = null;
        $dbfContent = null;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, ['json', 'geojson'])) {
                $geojsonContent = $zip->getFromIndex($i);
                break;
            } elseif ($ext === 'dbf' && !$dbfContent) {
                $dbfContent = $zip->getFromIndex($i);
            }
        }

        if ($geojsonContent) {
            $decoded = json_decode($geojsonContent, true);
            $features = $decoded['features'] ?? [];
        } elseif ($dbfContent) {
            $tmpDbfPath = tempnam(sys_get_temp_dir(), 'dbfzip_');
            file_put_contents($tmpDbfPath, $dbfContent);
            $features = $this->parseDbfFile($tmpDbfPath);
            @unlink($tmpDbfPath);
        }

        $zip->close();
        return $features;
    }
}