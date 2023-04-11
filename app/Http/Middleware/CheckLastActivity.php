<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;

class CheckLastActivity
{
    public function handle($request, Closure $next)
    {
        // Memeriksa apakah pengguna sudah logind
        if (Auth::check()) {
            // Mendapatkan waktu terakhir aktivitas pengguna dari kolom last_activity
            $lastActivity = Auth::user()->last_activity;
            // dd($lastActivity);

            // Memeriksa selisih waktu antara sekarang dan last_activity
            $now = Carbon::now();
            $diffInMinutes = $now->diffInMinutes($lastActivity);

            // Jika selisih waktu lebih dari 10 menit, maka pengguna di-logout secara otomatis
            if ($diffInMinutes > env('AUTO_LOGOUT', 10)) {
                Auth::logout();
                return response()->json(['message' => 'Session expired.'], 401);
            }

            // Mengupdate kolom last_activity dengan waktu sekarang
            Auth::user()->update(['last_activity' => $now]);
        }

        return $next($request);
    }
}
