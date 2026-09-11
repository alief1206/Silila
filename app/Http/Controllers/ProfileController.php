<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request, string $id)
    {


        $data = $request->all();

        // Update password jika ada dan sesuai dengan konfirmasi
        $password = $request->input('password');
        if ($password) {
            $data['password'] = Hash::make($password);
        }

        $item = User::findOrFail($id);
        $item->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
