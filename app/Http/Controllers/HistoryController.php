<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    //

    public function index()
    {
        $riwayat = DB::table('riwayat')
            ->leftJoin('users', 'users.id', '=', 'riwayat.user_id') // Gunakan user_id jika ada di tabel riwayat
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
                'riwayat.created_at as tgl',
                'users.nama as namauser' // Pastikan nama kolom sesuai dengan yang ada di tabel users
            )
            ->paginate(25);

        $no = 1;
        return view('dashboard.history', compact('riwayat', 'no'));
    }
}
