<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    public function simpan(Request $request) {
        $file = $request->file('dokumen'); // Mendapatkan file dokumen dari request
        $path = $file->store('public'); // Menyimpan file ke folder 'public' di local disk

        $url = Storage::url($path); // Mendapatkan URL yang dienkripsi untuk mengakses file
        return response()->json(['url' => $url]); // Menyajikan url file yang dienkripsi ke pengguna
        // return response()->file(Storage::path($path)); // Menyajikan file yang dienkripsi ke pengguna
    }

    public function downloadFile(Request $request)
    {
        $file = $request->input('file_name');

        $storage_path = storage_path('app/public/' . $file);
        $path = realpath($storage_path); // Menggunakan realpath untuk memastikan path yang valid
        if (!$path || !Str::startsWith($path, storage_path('app\public\\'))) {
            return response()->json(['success' => false], 403); // Melakukan validasi tambahan terhadap path yang dihasilkan
        }
        return response()->download($path);
    }

    public function download(Request $request)
    {
        $fileName = $request->input('file_name');
        $filePath = 'C:/traspac/xampp_74/htdocs/riset/sanctum/storage/app/public/' . $fileName; // path file yang diterima dari input pengguna

        // Cek apakah file ada sebelum di-download
        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            return response()->json(['error' => 'File not found.', 'path'=> $filePath]);
        }
    }


}
