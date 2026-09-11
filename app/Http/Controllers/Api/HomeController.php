<?php

namespace App\Http\Controllers\Api;

use App\Models\Geometri;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function get(Request $request, $long, $lat, $tipe)
    {
        // $lat = $request->input('lat');
        // $long = $request->input('long');
        // $tipe = $request->input('tipe');

        dd($lat);
        $query = Geometri::select(
            'geometri.id'
        );

        if (!empty($tipe)) {
            $query->where('tipe', '=', $tipe);

            if ($tipe == 1) {
                $query->join('data_lp2b', 'geometri.id', '=', 'data_lp2b.geometri_id')
                    ->join('desa', 'data_lp2b.desa_id', '=', 'desa.id')
                    ->join('kecamatan', 'desa.kecamatan_id', '=', 'kecamatan.id')
                    ->select(
                        'geometri.koordinat',
                        'geometri.tipe',
                        'data_lp2b.*',
                        'desa.nama as desa',
                        'kecamatan.nama as kecamatan'
                    );

                $desa = $request->header('desa');

                if (!empty($desa)) {
                    $query->where('data_lp2b.desa_id', '=', $desa);
                }
            } else if ($tipe == 2) {
                $query->join('data_lsd', 'geometri.id', '=', 'data_lsd.geometri_id')
                    ->select(
                        'geometri.koordinat',
                        'geometri.tipe',
                        'data_lsd.*'
                    );
            } else if ($tipe == 3) {
                // Handle tipe 3
            }
        }

        // Ubah input lat dan long menjadi satu koordinat yang sesuai dengan format di kolom 'koordinat'
        $coordinate = "$lat,$long,0";
        // $coordinate = "113.95586872466197,-8.547076795857379,0";

        // Tambahkan kondisi pencarian berdasarkan lokasi koordinat
        if (!empty($coordinate)) {
            $query->where('geometri.koordinat', '=', $coordinate);
        }

        if ($query->count() == 0) {
            $result['data'] = [];
            $result['error'] = true;
            $result['message'] = "Data tidak ditemukan";
            return response($result);
        }

        $data = $query->orderBy('geometri.id', 'desc')->get();

        $result['data'] = $data;
        $result['error'] = false;
        $result['message'] = "Success";
        return response($result);
    }
}
