<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    
    public function handle(Request $request, Closure $next, $role)
    {
        // Cek apakah pengguna memiliki peran yang diperlukan
        if (!$request->user() || $request->user()->role !== $role) {
            // Jika tidak, lempar exception atau redirect ke halaman yang sesuai
            return response()->json(['success' => false, 'message' => 'unauthorized'], 403);
        }

        return $next($request);
    }
}
