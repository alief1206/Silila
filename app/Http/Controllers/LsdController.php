<?php

namespace App\Http\Controllers;

use App\Models\DataLsd;
use Illuminate\Http\Request;

class LsdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lsd = DataLsd::paginate(10);
        return view("dashboard.lsd.lsd", compact("lsd"));
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
        $validatedData = $request->validate([
            'geometri_id' => 'required',
            'lsd' => 'required|string|max:150',
            'hutan' => 'required|string|max:100',
            'luas' => 'required|string|max:100',
            'ket' => 'nullable|string|max:100',
            'irigasi_pr' => 'nullable|string|max:100',
            'kewenangan' => 'nullable|string|max:100',
            'ip' => 'nullable|string|max:10',
            'prod' => 'nullable|string|max:10',
            'irigasi' => 'nullable|string|max:100',
            'kondisigab' => 'nullable|string|max:100',
            'kontamgab' => 'nullable|string|max:100',
            'polru' => 'nullable|string|max:100',
            'asalrtr' => 'nullable|string|max:100',
            'fpgab_1' => 'nullable|string|max:100',
            'ba' => 'required|string|max:100',
            'tipehak' => 'nullable|string|max:100',
            'luascea_hm' => 'nullable|string|max:100',
            'golluas_hm' => 'nullable|string|max:100',
            'golluas_hm2' => 'nullable|string|max:100',
            'hmkeluar' => 'nullable|string|max:100',
            'investasi' => 'nullable|string|max:100',
        ]);

        DataLSD::create($validatedData);

        return redirect()->route('dashboardLsd')->with('success', 'Data LSD berhasil ditambahkan.');
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
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'geometri_id' => 'required',
            'lsd' => 'required|string|max:150',
            'hutan' => 'required|string|max:100',
            'luas' => 'required|string|max:100',
            'ket' => 'nullable|string|max:100',
            'irigasi_pr' => 'nullable|string|max:100',
            'kewenangan' => 'nullable|string|max:100',
            'ip' => 'nullable|string|max:10',
            'prod' => 'nullable|string|max:10',
            'irigasi' => 'nullable|string|max:100',
            'kondisigab' => 'nullable|string|max:100',
            'kontamgab' => 'nullable|string|max:100',
            'polru' => 'nullable|string|max:100',
            'asalrtr' => 'nullable|string|max:100',
            'fpgab_1' => 'nullable|string|max:100',
            'ba' => 'required|string|max:100',
            'tipehak' => 'nullable|string|max:100',
            'luascea_hm' => 'nullable|string|max:100',
            'golluas_hm' => 'nullable|string|max:100',
            'golluas_hm2' => 'nullable|string|max:100',
            'hmkeluar' => 'nullable|string|max:100',
            'investasi' => 'nullable|string|max:100',
        ]);

        $datalsd = DataLSD::findOrFail($id);
        $datalsd->update($validatedData);

        return redirect()->route('dashboardLsd')->with('success', 'Data LSD berhasil diperbarui.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
