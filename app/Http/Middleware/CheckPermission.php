<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        // Ambil izin akses berdasarkan nama
        $permission = Permission::where('name', $permission)->first();
         // Jika izin akses tidak ditemukan, kembalikan response dengan pesan error
        if (!$permission) {
            return response()->json(['error' => 'Permission not found'], 403);
        }

        // Ambil peran (role) pengguna saat ini
        // $userRole = $request->user()->role; //  bisa diganti sesuai struktur tabel user 
        $userRole = Role::where('name', $request->user()->role)->first();
        // $permission = Permission::where('name', 'create-post')->first();

        // Cek apakah peran pengguna memiliki izin akses yang diperlukan
        if (!$userRole->permissions->contains($permission)) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Anda tidak Memiliki hak akses.'], 403);
        }

        return $next($request);
    }
}
