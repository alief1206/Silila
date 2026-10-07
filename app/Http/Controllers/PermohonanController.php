<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PermohonanSurat;

class PermohonanController extends Controller
{
    public function index()
    {
        $permohonan = PermohonanSurat::orderBy('created_at', 'desc')->get();
        return view('dashboard.permohonan.index', compact('permohonan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'catatan_admin' => 'nullable|string',
            'file_surat_balasan' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120'
        ]);

        $permohonan = PermohonanSurat::findOrFail($id);
        $permohonan->status = $request->status;
        if ($request->has('catatan_admin')) {
            $permohonan->catatan_admin = $request->catatan_admin;
        }

        if ($request->hasFile('file_surat_balasan')) {
            $file = $request->file('file_surat_balasan');
            $filename = time() . '_balasan_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/permohonan'), $filename);
            $permohonan->file_surat_balasan = 'uploads/permohonan/' . $filename;
        }

        $permohonan->save();

        return redirect()->back()->with('success', 'Status permohonan berhasil diperbarui.');
    }
}
