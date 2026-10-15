<?php

namespace App\Http\Controllers\Api;

use App\Models\Desa;
use App\Models\DataLsd;
use App\Models\Riwayat;
use App\Models\DataLp2b;
use App\Models\Geometri;
use App\Models\Kecamatan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class GeometriController extends Controller
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    public function all(Request $request)
    {
        ini_set('memory_limit', '500G');
        $tipe = $request->input('tipe');

        $query = Geometri::select(
            'geometri.id'
        );

        if (!empty($tipe)) {
            if ($tipe == 1) {
                $query->where('tipe', '=', $tipe);

                $query->join('data_lp2b', 'geometri.id', '=', 'data_lp2b.geometri_id')
                    ->join('desa', 'geometri.desa_id', '=', 'desa.id')
                    ->join('kecamatan', 'desa.kecamatan_id', '=', 'kecamatan.id')
                    ->select(
                        DB::raw('ST_AsGeoJSON(geometri.koordinat) as koordinat'),
                        'geometri.tipe',
                        'data_lp2b.*',
                        'desa.nama as desa',
                        'kecamatan.nama as kecamatan'
                    );
            } else if ($tipe == 2) {
                $query->where('tipe', '=', $tipe);

                $query->join('data_lsd', 'geometri.id', '=', 'data_lsd.geometri_id')
                    ->join('desa', 'geometri.desa_id', '=', 'desa.id')
                    ->join('kecamatan', 'desa.kecamatan_id', '=', 'kecamatan.id')
                    ->select(
                        DB::raw('ST_AsGeoJSON(geometri.koordinat) as koordinat'),
                        'geometri.tipe',
                        'data_lsd.*',
                        'desa.nama as desa',
                        'kecamatan.nama as kecamatan'
                    );
            } else if ($tipe == 3) {
                # code...
            } else if (strpos($tipe, ',') !== false) {
                $tipeArray = explode(',', $tipe);
                $query->whereIn('tipe', $tipeArray);
                $query->leftJoin('desa', 'geometri.desa_id', '=', 'desa.id')
                    ->leftJoin('kecamatan', 'desa.kecamatan_id', '=', 'kecamatan.id')
                    ->select(
                        DB::raw('ST_AsGeoJSON(geometri.koordinat) as koordinat'),
                        'geometri.tipe',
                        'desa.nama as desa',
                        'kecamatan.nama as kecamatan',
                        'geometri.id as geometri_id',
                    );
            }

            $kecamatan = $request->header('kecamatan');
            $desa = $request->header('desa');

            if (!empty($kecamatan)) {
                $query->where('desa.kecamatan_id', '=', $kecamatan);
            }

            if (!empty($desa)) {
                $query->where('geometri.desa_id', '=', $desa);
            }
        }

        $data = $query->orderBy('geometri.id', 'desc')->get();

        if ($query->count() == 0) {
            $result['data']             = [];
            $result['error']           = true;
            $result['message']          = "Data tidak ditemukan";
            return response($result);
        }


        $result['data']             = $data;
        $result['error']           = false;
        $result['message']          = "Success";
        return response($result);
    }

    public function getKecamatan()
    {
        $kecamatan = Kecamatan::all();
        return response()->json($kecamatan);
    }
    public function getDesa($kecamatanId)
    {
        $desa = Desa::where('kecamatan_id', $kecamatanId)->get();
        return response()->json($desa);
    }
    public function get_data(Request $request, $tipe, $geometri_id)
    {
        $query = Geometri::select(
            'geometri.id'
        );


        if (!empty($tipe)) {
            $query->where('tipe', '=', $tipe);
            if ($tipe == 1) {
                $query->join('data_lp2b', 'geometri.id', '=', 'data_lp2b.geometri_id')
                    ->join('desa', 'geometri.desa_id', '=', 'desa.id')
                    ->join('kecamatan', 'desa.kecamatan_id', '=', 'kecamatan.id')
                    ->select(
                        'geometri.tipe',
                        'data_lp2b.*',
                        'desa.nama as desa',
                        'kecamatan.nama as kecamatan'
                    );
            } else if ($tipe == 2) {
                $query->join('data_lsd', 'geometri.id', '=', 'data_lsd.geometri_id')
                    ->join('desa', 'geometri.desa_id', '=', 'desa.id')
                    ->join('kecamatan', 'desa.kecamatan_id', '=', 'kecamatan.id')
                    ->select(
                        'geometri.tipe',
                        'data_lsd.*'
                    );
            } else if ($tipe == 3) {
                # code...
            }
        }

        $query = $query->where('geometri.id', $geometri_id);


        if ($query->count() == 0) {
            $result['data']             = [];
            $result['error']           = true;
            $result['message']          = "Data tidak ditemukan";
            return response($result);
        }

        $data = $query->orderBy('geometri.id', 'desc')->get()[0];

        $result['data']             = $data;
        $result['error']           = false;
        $result['message']          = "Success";
        return response($result);
    }

    public function index(Request $request)
    {
        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);
        $draw   = $request->input('draw');
        $search = $request->input('search.value');


        $query = DB::table('geometri')
            ->join('desa', 'geometri.desa_id', '=', 'desa.id')
            ->join('kecamatan', 'desa.kecamatan_id', '=', 'kecamatan.id')
            ->select(
                'geometri.id',
                'geometri.tipe',
                'geometri.desa_id',
                'geometri.object_id',
                DB::raw('ST_AsGeoJSON(geometri.koordinat) as koordinat'),
                'desa.nama as desa',
                'kecamatan.nama as kecamatan'
            )
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('desa.nama', 'like', "%$search%")
                        ->orWhere('kecamatan.nama', 'like', "%$search%")
                        ->orWhere('geometri.object_id', 'like', "%$search%");
                });
            });

        $count = $query->count();
        $data = $query->orderBy('geometri.id', 'desc')->skip($start)->take($length)->get();

        if ($count == 0) {
            $result = [
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'draw' => $draw,
                'data' => [],
                'error' => true,
                'message' => 'Data tidak ditemukan',
            ];
            return response($result);
        }

        $result = [
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'draw' => $draw,
            'data' => $data,
            'error' => false,
            'message' => 'OK',
        ];

        return response($result);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'object_id' => 'required|numeric|unique:geometri,object_id',
            'desa_id' => 'required|numeric',
            'kp2b' => 'required',
            'ket' => 'required',
            'luas' => 'required',
            'koordinat' => 'required',
            'tipe' => 'required|numeric',
        ], [
            'required' => ':attribute harus diisi.',
            'numeric' => ':attribute harus berupa numeric.',
            'unique' => ':attribute harus unik, value telah digunakan!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => Str::ucfirst($validator->errors()->first()),
                'data' => null
            ]);
        }

        $sv = Geometri::create([
            'object_id' => $request->object_id,
            'desa_id' => $request->desa_id,
            'kp2b' => $request->kp2b,
            'ket' => $request->ket,
            'luas' => $request->luas,
            'koordinat' => DB::raw("ST_GeomFromGeoJSON('" . $request->koordinat . "')"),
            'tipe' => $request->tipe
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Geometri berhasil ditambahkan.',
            'data' => null
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'desa_id' => 'required|numeric',
            'kp2b' => 'required',
            'ket' => 'required',
            'luas' => 'required',
            'koordinat' => 'required',
            'tipe' => 'required|numeric',
        ], [
            'required' => ':attribute harus diisi.',
            'numeric' => ':attribute harus berupa numeric.',
            'unique' => ':attribute harus unik, value telah digunakan!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => Str::ucfirst($validator->errors()->first()),
                'data' => null
            ]);
        }

        $dataGeometri = [
            'desa_id' => $request->desa_id,
            'kp2b' => $request->kp2b,
            'ket' => $request->ket,
            'luas' => $request->luas,
            'koordinat' => DB::raw("ST_GeomFromGeoJSON('" . $request->koordinat . "')"),
            'tipe' => $request->tipe
        ];

        $geometri = Geometri::findOrFail($id);
        $geometri->update($dataGeometri);

        return response()->json([
            'error' => false,
            'message' => 'Geometri berhasil diubah.',
            'data' => null
        ]);
    }

    public function destroy($id)
    {
        $geometri = Geometri::findOrFail($id);
        $geometri->delete();

        return response()->json([
            'error' => false,
            'message' => 'Geometri berhasil dihapus.',
            'data' => null
        ]);
    }

    public function import_lp2b(Request $request)
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');

        $tempFilePath = null;
        $shouldUnlink = false;

        if ($request->hasFile('file')) {
            $tempFilePath = $request->file('file')->getRealPath();
        } elseif ($request->filled('file')) {
            $base64Data = $request->input('file');
            $file_type = explode(';base64,', $base64Data);
            $file_type = explode('data:', $file_type[0]);
            $file_type = explode('/', $file_type[1] ?? '');
            $data_type = $file_type[0] ?? '';
            $app_type = $file_type[1] ?? '';
            $file_convert = str_replace("data:$data_type/" . $app_type . ';base64,', '', $base64Data);
            $file_convert = str_replace(' ', '+', $file_convert);

            $geoJSONData = base64_decode($file_convert);

            $uploadPath = public_path('uploads');
            File::makeDirectory($uploadPath, 0755, true, true);
            $filename = 'spatial_temp_lp2b_' . time() . '.json';
            $tempFilePath = $uploadPath . '/' . $filename;
            file_put_contents($tempFilePath, $geoJSONData);
            $shouldUnlink = true;
        }

        if (empty($tempFilePath) || !file_exists($tempFilePath)) {
            return response([
                'error' => true,
                'message' => "Upload gagal. Pastikan file spasial telah dipilih!"
            ]);
        }

        try {
            $importer = app(\App\Services\Lp2bStreamingImporter::class);
            $stats = $importer->import($tempFilePath);

            if ($shouldUnlink) {
                @unlink($tempFilePath);
            }

            return response([
                'error' => false,
                'message' => sprintf(
                    "Upload data LP2B berhasil! Total dipindai: %s bidang (Baru: %s, Diperbarui: %s, Dilewati: %s)",
                    number_format($stats['total_scanned']),
                    number_format($stats['inserted']),
                    number_format($stats['updated']),
                    number_format($stats['skipped'])
                )
            ]);
        } catch (\Exception $e) {
            if ($shouldUnlink) {
                @unlink($tempFilePath);
            }
            Log::error('Gagal import_lp2b API: ' . $e->getMessage());
            return response([
                'error' => true,
                'message' => "Gagal menyimpan data LP2B: " . $e->getMessage()
            ]);
        }
    }

    public function import_lsd(Request $request)
    {
        set_time_limit(0);

        $base64Data = $request->input('file');

        if (empty($base64Data)) {
            return response([
                'error' => true,
                'message' => "Upload gagal. Pastikan data telah terisi!"
            ]);
        }

        $file_type = explode(';base64,', $base64Data);
        $file_type = explode('data:', $file_type[0]);
        $file_type = explode('/', $file_type[1] ?? '');
        $data_type = $file_type[0] ?? '';
        $app_type = $file_type[1] ?? '';
        $file_convert = str_replace("data:$data_type/" . $app_type . ';base64,', '', $base64Data);
        $file_convert = str_replace(' ', '+', $file_convert);

        $geoJSONData = base64_decode($file_convert);

        // Simpan file sementara
        $uploadPath = public_path('uploads');
        File::makeDirectory($uploadPath, 0755, true, true);
        $filename = 'spatial_temp_lsd_' . time();
        $tempFilePath = $uploadPath . '/' . $filename;
        file_put_contents($tempFilePath, $geoJSONData);

        try {
            $importer = app(\App\Services\LsdStreamingImporter::class);
            $stats = $importer->import($tempFilePath);

            @unlink($tempFilePath);

            $pesan = sprintf(
                "Upload data LSD berhasil! Total: %s | Baru: %s | Diperbarui: %s | Dilewati: %s",
                number_format($stats['total_scanned']),
                number_format($stats['inserted']),
                number_format($stats['updated']),
                number_format($stats['skipped'])
            );

            return response([
                'error' => false,
                'message' => $pesan,
                'data' => $stats
            ]);
        } catch (\Throwable $e) {
            @unlink($tempFilePath);
            Log::error('Gagal import_lsd API: ' . $e->getMessage());
            return response([
                'error' => true,
                'message' => "Gagal memproses data LSD: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper untuk mengekstrak array features dari temp file (GeoJSON, ZIP, DBF)
     */
    private function extractFeaturesFromPath($filePath)
    {
        $content = @file_get_contents($filePath);
        if (!$content) {
            return [];
        }

        // 1. Coba decode GeoJSON / JSON
        $data = json_decode($content, true);
        if (is_array($data) && isset($data['features'])) {
            return $data['features'];
        }

        // 2. Coba sebagai ZIP Archive
        if (class_exists('\ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
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
                    $features = $this->parseDbfPath($tmpDbfPath);
                    @unlink($tmpDbfPath);
                }

                $zip->close();
                if (!empty($features)) {
                    return $features;
                }
            }
        }

        // 3. Coba sebagai file DBF binary
        return $this->parseDbfPath($filePath);
    }

    private function parseDbfPath($filePath)
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

    public function addRiwayat(Request $request)
    {
        $data = $request->all();
        Riwayat::create($data);
        return response([
            'error' => false,
            'message' => "Upload berhasil!"
        ]);
    }
    public function getUserData()
    {
        return response()->json(['userData' => auth()->user()]);
    }
    public function checkAuth()
    {
        if (Auth::check()) {
            return response()->json(['authenticated' => true, 'user' => Auth::user()]);
        } else {
            return response()->json(['authenticated' => false]);
        }
    }

    /**
     * Search-on-Demand: Cek apakah titik koordinat berada di dalam kawasan LSD / LP2B
     * menggunakan kueri spasial native ST_Contains / ST_Intersects.
     */
    public function checkLocation(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng', $request->input('long'));

        if (!is_numeric($lat) || !is_numeric($lng)) {
            return response()->json([
                'error' => true,
                'message' => 'Parameter lat dan lng harus berupa angka desimal yang valid.',
            ], 422);
        }

        $lat = (float)$lat;
        $lng = (float)$lng;
        $pointWkt = "POINT({$lng} {$lat})";

        // Cari geometri yang menaungi titik tersebut
        $matched = Geometri::with(['desa.kecamatan', 'lsd'])
            ->select(
                'geometri.*',
                DB::raw('ST_AsGeoJSON(geometri.koordinat) as geojson')
            )
            ->where(function ($q) use ($pointWkt) {
                $q->whereRaw("ST_Contains(koordinat, ST_GeomFromText('{$pointWkt}', 4326))")
                  ->orWhereRaw("ST_Contains(koordinat, ST_GeomFromText('{$pointWkt}'))")
                  ->orWhereRaw("ST_Intersects(koordinat, ST_GeomFromText('{$pointWkt}', 4326))")
                  ->orWhereRaw("ST_Intersects(koordinat, ST_GeomFromText('{$pointWkt}'))");
            })
            ->get();

        if ($matched->isEmpty()) {
            return response()->json([
                'error' => false,
                'found' => false,
                'message' => 'Titik koordinat berada di LUAR kawasan LSD dan LP2B.',
                'lat' => $lat,
                'lng' => $lng,
            ]);
        }

        $results = [];
        foreach ($matched as $item) {
            $isLsd = ($item->tipe == 2);
            $lsdRecord = $item->lsd ? $item->lsd->first() : null;
            $rawProps = [];
            if ($lsdRecord && !empty($lsdRecord->properties)) {
                $rawProps = is_array($lsdRecord->properties) ? $lsdRecord->properties : json_decode($lsdRecord->properties, true);
            }

            $desaNama = optional($item->desa)->nama ?? ($rawProps['DESA'] ?? '-');
            $kecNama  = optional(optional($item->desa)->kecamatan)->nama ?? ($rawProps['KECAMATAN'] ?? '-');

            $results[] = [
                'geometri_id'       => $item->id,
                'object_id'         => $item->object_id ?? ($rawProps['OBJECTID'] ?? null),
                'tipe'              => $item->tipe,
                'tipe_nama'         => $isLsd ? 'Lahan Sawah Dilindungi (LSD)' : ($item->tipe == 1 ? 'LP2B' : 'LBS'),
                'desa'              => $desaNama,
                'kecamatan'         => $kecNama,
                'geojson'           => json_decode($item->geojson, true),
                'kategori' => [
                    'info_utama' => [
                        'status_lsd'  => $lsdRecord->status_lsd ?? ($rawProps['LSD'] ?? ($isLsd ? 'LSD' : '-')),
                        'luas'        => $lsdRecord->luas ?? ($rawProps['LUAS'] ?? '-'),
                        'nomor_ba'    => $lsdRecord->ba ?? ($rawProps['BA'] ?? '-'),
                        'keterangan'  => $rawProps['KET'] ?? ($isLsd ? 'Masuk Kawasan Lahan Sawah Dilindungi' : '-'),
                    ],
                    'pertanian_irigasi' => [
                        'irigasi'         => $lsdRecord->irigasi ?? ($rawProps['IRIGASI'] ?? '-'),
                        'irigasi_primer'  => $rawProps['Irigasi_Pr'] ?? '-',
                        'kewenangan'      => $lsdRecord->kewenangan ?? ($rawProps['Kewenangan'] ?? '-'),
                        'ip'              => $lsdRecord->ip ?? ($rawProps['IP'] ?? '-'),
                        'prod'            => $lsdRecord->prod ?? ($rawProps['PROD'] ?? '-'),
                        'kondisi_irigasi' => $rawProps['KondisiGab'] ?? '-',
                    ],
                    'tata_ruang' => [
                        'pola_ruang'       => $lsdRecord->polru ?? ($rawProps['Polru'] ?? '-'),
                        'asal_rtr'         => $rawProps['AsalRTR'] ?? '-',
                        'status_hutan'     => $lsdRecord->hutan ?? ($rawProps['HUTAN'] ?? '-'),
                        'fungsi_pertanian' => $rawProps['FPGab_1'] ?? '-',
                    ],
                    'pertanahan_investasi' => [
                        'tipe_hak'             => $lsdRecord->tipehak ?? ($rawProps['TIPEHAK'] ?? '-'),
                        'investasi'            => $lsdRecord->investasi ?? ($rawProps['INVESTASI'] ?? '-'),
                        'luas_dikecualikan_hm' => $lsdRecord->luascea_hm ?? ($rawProps['LuasCEA_HM'] ?? '-'),
                    ],
                    'administrasi_teknis' => [
                        'desa'       => $desaNama,
                        'kecamatan'  => $kecNama,
                        'sumber'     => $rawProps['SUMBER'] ?? 'Kementerian ATR/BPN',
                        'object_id'  => $item->object_id ?? ($rawProps['OBJECTID'] ?? '-'),
                    ],
                ],
                'raw_properties'    => $rawProps,
            ];
        }

        return response()->json([
            'error'   => false,
            'found'   => true,
            'count'   => count($results),
            'lat'     => $lat,
            'lng'     => $lng,
            'data'    => $results[0], // Bidang utama yang menaungi
            'semua'   => $results,
            'message' => 'Titik koordinat berhasil diverifikasi berada di dalam batas zonasi.',
        ]);
    }
}
