<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }
        
        $userRole = auth()->user()->role;
        // Super Admin = 1, Admin = 2 (Assuming this based on DB). 
        // We will map string roles to DB values.
        $roleMap = [
            'superadmin' => 1,
            'admin' => 2
        ];
        
        $hasRole = false;
        foreach ($roles as $role) {
            if (isset($roleMap[$role]) && $userRole == $roleMap[$role]) {
                $hasRole = true;
                break;
            }
        }
        
        if (!$hasRole) {
            abort(403, 'Unauthorized access.');
        }
        
        return $next($request);
    }
}
