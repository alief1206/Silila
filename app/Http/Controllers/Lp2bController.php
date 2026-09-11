<?php

namespace App\Http\Controllers;

use App\Models\DataLp2b;
use Illuminate\Http\Request;

class Lp2bController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lp2b = DataLp2b::with('geometri')->paginate(25);
        return view("dashboard.lp2b.lp2b", compact("lp2b"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data yang dikirim oleh pengguna
        $validatedData = $request->validate([
            'geometri_id' => 'required',
            'desa_id' => 'required',
            'kp2b' => 'required|string',
            'ket' => 'required|string',
            'luas' => 'required|string',
        ]);

        try {
            // Simpan data lp2b ke dalam database
            $lp2b = DataLp2b::create([
                'geometri_id' => $validatedData['geometri_id'],
                'desa_id' => $validatedData['desa_id'],
                'kp2b' => $validatedData['kp2b'],
                'ket' => $validatedData['ket'],
                'luas' => $validatedData['luas'],
            ]);

            // Berikan respons sesuai kebutuhan Anda, misalnya respons berhasil atau alihkan ke halaman lain
            return redirect()->route('dashboard.lp2b')->with('success', 'Data LP2B berhasil disimpan.');
        } catch (\Exception $e) {
            // Tangani kesalahan jika terjadi
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data LP2B. Silakan coba lagi.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'geometri_id' => 'required|integer',
            'desa_id' => 'required', // Foto produk menjadi opsional untuk diubah
            'kp2b' => 'required|string|max:50',
            'ket' => 'required|string|max:100',
            'luas' => 'required|string|max:100',

        ]);

        // Dapatkan data produk berdasarkan ID
        $lp2b = DataLp2b::findOrFail($id);

        // Perbarui data lp2$lp2b
        $lp2b->geometri_id = $validatedData['geometri_id'];
        $lp2b->desa_id = $validatedData['desa_id'];
        $lp2b->kp2b = $validatedData['kp2b'];
        $lp2b->ket = $validatedData['ket'];
        $lp2b->luas = $validatedData['luas'];
        $lp2b->save();
        return redirect()->route('dashboard.lp2b')->with('success', 'Data geometri berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lp2b = DataLp2b::find($id);
        $lp2b->delete();
        return redirect()->route('dashboard.lp2b');
    }
}
