<?php

namespace App\Http\Controllers\Api;

use App\Models\Desa;
use App\Models\Geometri;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(){
        $data['jumlahKecamatan'] = number_format(Kecamatan::all()->count(), 0, '.', ',');
        $data['jumlahDesa'] = number_format(Desa::all()->count(), 0, ',', '.');
        $data['jumlahKp2b'] = number_format(Geometri::join('data_lp2b', 'data_lp2b.geometri_id', 'geometri.id')->where('tipe', 1)->select(DB::raw('SUM(luas) as total'))->get()[0]->total, 3, ',', '.');
        $data['jumlahLsd'] = number_format(Geometri::join('data_lsd', 'data_lsd.geometri_id', 'geometri.id')->where('tipe', 2)->select(DB::raw('SUM(luas) as total'))->get()[0]->total, 3, ',', '.');
        $data['jumlahLbs'] = 0;
        // $data['jumlahLbs'] = number_format(Geometri::->join()->where('tipe', 3)->count(), 0, ',', '.');

        $result['error'] = false;
        $result['message'] = "OK";
        $result['data'] = $data;
        return response($result);
    }

    public function pieChart(){
        $data = Geometri::selectRaw('CASE tipe
                                    WHEN 1 THEN "LP2B"
                                    WHEN 2 THEN "LSD"
                                    WHEN 3 THEN "LBS"
                                    ELSE "Tidak Diketahui"
                                  END AS ket,
                                  count(id) as jumlah')
                                  ->groupBy('tipe')
                                  ->get();

        $result['error'] = false;
        $result['message'] = "OK";
        $result['data'] = $data;
        return response($result);
    }

    public function getDesa(Request $request, $kecamatan_id){
        $search = $request->input('search');

        $query = Desa::select('*');

        if (!empty($kecamatan_id)) {
            $query->where('kecamatan_id', $kecamatan_id);
        }

        if(!empty($search)){
            $query = $query->where(function($query) use ($search){
                $query->where('nama','like',"%$search%");
            });
        }

        if($query->count() == 0){
            $result['data']             = [];
            $result['status']           = false;
            $result['message']          = "Data tidak ditemukan";
            return response($result);
        }

        $data = $query->orderBy('id','asc')->get();

        $result['data']             = $data;
        $result['status']           = true;
        $result['message']          = "Success";
        return response($result);
    }

    public function getKecamatan(Request $request){
        $search = $request->input('search');

        $query = Kecamatan::select('*');

        if(!empty($search)){
            $query = $query->where(function($query) use ($search){
                $query->where('nama','like',"%$search%");
            });
        }

        if($query->count() == 0){
            $result['data']             = [];
            $result['status']           = false;
            $result['message']          = "Data tidak ditemukan";
            return response($result);
        }

        $data = $query->orderBy('id','asc')->get();

        $result['data']             = $data;
        $result['status']           = true;
        $result['message']          = "Success";
        return response($result);
    }
}
