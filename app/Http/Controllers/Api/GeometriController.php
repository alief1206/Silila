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
        $file_type = explode('/', $file_type[1]);
        $data_type = $file_type[0];
        $app_type = $file_type[1];
        $file_convert = str_replace("data:$data_type/" . $app_type . ';base64,', '', $base64Data);
        $file_convert = str_replace(' ', '+', $file_convert);

        $geoJSONData = base64_decode($file_convert);

        // Simpan file sementara
        $uploadPath = public_path('uploads');
        File::makeDirectory($uploadPath, 0755, true, true);
        $filename = 'geojson_temp_' . time() . '.json';
        $tempFilePath = $uploadPath . '/' . $filename;
        file_put_contents($tempFilePath, $geoJSONData);

        // Baca data JSON
        $geojson = json_decode(file_get_contents($tempFilePath), true);
        $jumlahError = 0;
        foreach ($geojson['features'] as $feature) {
            $KP2B = $feature['properties']['KP2B'];
            $KET = $feature['properties']['KET'];
            $KECAMATAN = $feature['properties']['KECAMATAN'];
            $DESA = $feature['properties']['DESA'];
            $LUAS = $feature['properties']['LUAS'];
            $coordinates = $feature['geometry']['coordinates'];

            // Validasi dan simpan data kecamatan
            $dataKecamatan = Kecamatan::firstOrCreate(['nama' => $KECAMATAN]);

            // Validasi dan simpan data desa
            $dataDesa = Desa::firstOrCreate([
                'kecamatan_id' => $dataKecamatan->id,
                'nama' => $DESA
            ]);

            // Simpan data ke dalam database
            $geometri = Geometri::create([
                'desa_id' => $dataDesa->id,
                'koordinat' => DB::raw("ST_GeomFromGeoJSON('" . json_encode($feature['geometry']) . "')"),
                'tipe' => '1'
            ]);

            DataLp2b::create([
                'geometri_id' => $geometri->id,
                'kp2b' => $KP2B,
                'ket' => $KET,
                'luas' => $LUAS,
            ]);
        }

        // Hapus file sementara setelah membaca
        unlink($tempFilePath);

        return response([
            'error' => false,
            'message' => "Upload berhasil!"
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
        $file_type = explode('/', $file_type[1]);
        $data_type = $file_type[0];
        $app_type = $file_type[1];
        $file_convert = str_replace("data:$data_type/" . $app_type . ';base64,', '', $base64Data);
        $file_convert = str_replace(' ', '+', $file_convert);

        $geoJSONData = base64_decode($file_convert);

        // Simpan file sementara
        $uploadPath = public_path('uploads');
        File::makeDirectory($uploadPath, 0755, true, true);
        $filename = 'geojson_temp_' . time() . '.json';
        $tempFilePath = $uploadPath . '/' . $filename;
        file_put_contents($tempFilePath, $geoJSONData);

        // Baca data JSON
        $geojson = json_decode(file_get_contents($tempFilePath), true);
        $jumlahError = 0;
        foreach ($geojson['features'] as $feature) {
            $HUTAN = $feature['properties']['HUTAN'];
            $LUAS = $feature['properties']['LUAS'];
            $BA = $feature['properties']['BA'];
            $LuasCEA_HM = $feature['properties']['LuasCEA_HM'];
            $coordinates = $feature['geometry']['coordinates'];
            $KECAMATAN = $feature['properties']['KECAMATAN'];
            $DESA = $feature['properties']['DESA'];

            $firstCoordinate = $coordinates[0][0][0];
            $longitude = $firstCoordinate[0];
            $latitude = $firstCoordinate[1];

            // Validasi dan simpan data kecamatan
            $dataKecamatan = Kecamatan::firstOrCreate(['nama' => $KECAMATAN]);

            // Validasi dan simpan data desa
            $dataDesa = Desa::firstOrCreate([
                'kecamatan_id' => $dataKecamatan->id,
                'nama' => $DESA
            ]);

            // if ($BA != "KOREKSI") {
                // Simpan data ke dalam database
                $geometri = Geometri::create([
                    'desa_id' => $dataDesa->id,
                    'koordinat' => DB::raw("ST_GeomFromGeoJSON('" . json_encode($feature['geometry']) . "')"),
                    'tipe' => '2'
                ]);

                DataLsd::create([
                    'geometri_id' => $geometri->id,
                    'hutan' => $HUTAN,
                    'luas' => $LUAS,
                    'ba' => $BA,
                    'luascea_hm' => $LuasCEA_HM,
                ]);
            // }
        }

        // Hapus file sementara setelah membaca
        unlink($tempFilePath);

        return response([
            'error' => false,
            'message' => "Upload berhasil!"
        ]);
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
