<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Riwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {   // dd($riwayat);
        $searchKoordinat = $request->input('koordinat');
        Carbon::setLocale('id');
        $riwayat = DB::table('riwayat')
            ->leftJoin('geometri', 'geometri.id', '=', 'riwayat.geometri_id')
            ->leftJoin('data_lsd', 'geometri.id', '=', 'data_lsd.geometri_id')
            ->leftJoin('data_lp2b', 'geometri.id', '=', 'data_lp2b.geometri_id')
            ->leftJoin('desa', 'geometri.desa_id', '=', 'desa.id')
            ->leftJoin('kecamatan', 'desa.kecamatan_id', '=', 'kecamatan.id')
            ->select(
                DB::raw('CASE
                WHEN geometri.tipe = 1 THEN data_lp2b.luas
                WHEN geometri.tipe = 2 THEN data_lsd.luas
             END AS luas'),
                DB::raw('CASE
                WHEN geometri.tipe = 1 THEN data_lp2b.ket
                WHEN geometri.tipe = 2 THEN data_lsd.hutan
             END AS ket'),
                'desa.nama as desa',
                'kecamatan.nama as kecamatan',
                'riwayat.koordinat',
                'geometri.tipe',
                'riwayat.created_at as tgl'
            );
        if (Auth::check()) {
            # code...
            $riwayat->where('riwayat.user_id', Auth::user()->id);
        }
        // Jika ada pencarian koordinat, tambahkan kondisi WHERE
        if ($searchKoordinat) {
            $riwayat->where('riwayat.koordinat', 'like', '%' . $searchKoordinat . '%');
        }
        // Ambil hasil query sebagai array
        $riwayat = $riwayat->get();
        $user = auth()->user();
        // dd($riwayat);
        return view('home', compact('riwayat', 'user'));
    }
}
