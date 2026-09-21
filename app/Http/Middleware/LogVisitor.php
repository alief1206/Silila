<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $date = \Carbon\Carbon::today();

        // Cek apakah hari ini device/IP ini sudah akses
        $visitor = \App\Models\Visitor::where('ip_address', $ip)
            ->where('user_agent', $userAgent)
            ->whereDate('created_at', $date)
            ->first();

        if (!$visitor) {
            \App\Models\Visitor::create([
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'session_id' => session()->getId()
            ]);
        }

        return $next($request);
    }
}
