<?php

// app/Http/Middleware/CheckUserActivity.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        // Lakukan pengecekan hanya jika pengguna telah login
        if (Auth::check()) {
            $user = Auth::user(); // Dapatkan objek pengguna yang sedang login
            $lastActivity = $user->last_activity;

            // Periksa apakah pengguna aktif
            if ($lastActivity && now()->diffInMinutes($lastActivity) >= 1) {
                Auth::logout(); // Logout pengguna
                return redirect('/')->with('status', 'Anda telah logout karena tidak ada aktivitas dalam 5 menit.');
            }

            // Perbarui waktu aktivitas terakhir pengguna
            // $user->update(['last_activity' => now()]);
        }

        return $next($request);
    }
}
