<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request, string $id)
    {

        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif, webp.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data = $request->except(['password', 'foto']);

        // Update password jika ada
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $item = User::findOrFail($id);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($item->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($item->foto);
            }
            $fotoPath = $request->file('foto')->store('profile_photos', 'public');
            $data['foto'] = $fotoPath;
        }

        $item->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
