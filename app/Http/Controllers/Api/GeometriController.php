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
        $filename = 'spatial_temp_lp2b_' . time();
        $tempFilePath = $uploadPath . '/' . $filename;
        file_put_contents($tempFilePath, $geoJSONData);

        $features = $this->extractFeaturesFromPath($tempFilePath);

        if (empty($features)) {
            @unlink($tempFilePath);
            return response([
                'error' => true,
                'message' => "Format data spasial LP2B tidak valid / kosong."
            ]);
        }

        DB::beginTransaction();
        try {
            foreach ($features as $feature) {
                $p = $feature['properties'] ?? [];
                $KP2B = $p['KP2B'] ?? $p['kp2b'] ?? $p['Kp2b'] ?? null;
                $KET = $p['KET'] ?? $p['ket'] ?? $p['Ket'] ?? null;
                $KECAMATAN = $p['KECAMATAN'] ?? $p['kecamatan'] ?? $p['Kecamatan'] ?? 'BANYUWANGI';
                $DESA = $p['DESA'] ?? $p['desa'] ?? $p['Desa'] ?? 'DESA';
                $LUAS = $p['LUAS'] ?? $p['luas'] ?? $p['Luas'] ?? null;

                // Validasi dan simpan data kecamatan
                $dataKecamatan = Kecamatan::firstOrCreate(['nama' => $KECAMATAN]);

                // Validasi dan simpan data desa
                $dataDesa = Desa::firstOrCreate([
                    'kecamatan_id' => $dataKecamatan->id,
                    'nama' => $DESA
                ]);

                // Simpan data geometri ke dalam database
                $geomData = [
                    'desa_id' => $dataDesa->id,
                    'tipe' => '1'
                ];

                if (!empty($feature['geometry'])) {
                    $geomData['koordinat'] = DB::raw("ST_GeomFromGeoJSON('" . json_encode($feature['geometry']) . "')");
                }

                $geometri = Geometri::create($geomData);

                DataLp2b::create([
                    'geometri_id' => $geometri->id,
                    'kp2b' => $KP2B,
                    'ket' => $KET,
                    'luas' => $LUAS,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            @unlink($tempFilePath);
            Log::error('Gagal import_lp2b API: ' . $e->getMessage());
            return response([
                'error' => true,
                'message' => "Gagal menyimpan data LP2B: " . $e->getMessage()
            ]);
        }

        // Hapus file sementara setelah membaca
        @unlink($tempFilePath);

        return response([
            'error' => false,
            'message' => "Upload data LP2B berhasil!"
        ]);
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

        $features = $this->extractFeaturesFromPath($tempFilePath);

        if (empty($features)) {
            @unlink($tempFilePath);
            return response([
                'error' => true,
                'message' => "Format data spasial LSD tidak valid / kosong."
            ]);
        }

        DB::beginTransaction();
        try {
            foreach ($features as $feature) {
                $p = $feature['properties'] ?? [];
                $HUTAN = $p['HUTAN'] ?? $p['hutan'] ?? null;
                $LUAS = $p['LUAS'] ?? $p['luas'] ?? null;
                $BA = $p['BA'] ?? $p['ba'] ?? null;
                $LuasCEA_HM = $p['LuasCEA_HM'] ?? $p['luascea_hm'] ?? $p['LUASCEA_HM'] ?? null;
                $KECAMATAN = $p['KECAMATAN'] ?? $p['kecamatan'] ?? $p['Kecamatan'] ?? 'BANYUWANGI';
                $DESA = $p['DESA'] ?? $p['desa'] ?? $p['Desa'] ?? 'DESA';

                // Validasi dan simpan data kecamatan
                $dataKecamatan = Kecamatan::firstOrCreate(['nama' => $KECAMATAN]);

                // Validasi dan simpan data desa
                $dataDesa = Desa::firstOrCreate([
                    'kecamatan_id' => $dataKecamatan->id,
                    'nama' => $DESA
                ]);

                // Simpan data geometri ke dalam database
                $geomData = [
                    'desa_id' => $dataDesa->id,
                    'tipe' => '2'
                ];

                if (!empty($feature['geometry'])) {
                    $geomData['koordinat'] = DB::raw("ST_GeomFromGeoJSON('" . json_encode($feature['geometry']) . "')");
                }

                $geometri = Geometri::create($geomData);

                DataLsd::create([
                    'geometri_id' => $geometri->id,
                    'hutan' => $HUTAN,
                    'luas' => $LUAS,
                    'ba' => $BA,
                    'luascea_hm' => $LuasCEA_HM,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            @unlink($tempFilePath);
            Log::error('Gagal import_lsd API: ' . $e->getMessage());
            return response([
                'error' => true,
                'message' => "Gagal menyimpan data LSD: " . $e->getMessage()
            ]);
        }

        // Hapus file sementara setelah membaca
        @unlink($tempFilePath);

        return response([
            'error' => false,
            'message' => "Upload data LSD berhasil!"
        ]);
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
}
