<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\testController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [RegisteredUserController::class, 'store']);
});


// File
Route::prefix('dokumen')->group(function ($router) {
    Route::post('/simpan', [DokumenController::class, 'simpan']); // contoh enkripsi file
    Route::get('/download', [DokumenController::class, 'downloadFile']); // path traversal validation
    // Route::get('/download', [DokumenController::class, 'download']); // path traversal vulnerable
});


Route::group(['middleware' => ['auth:sanctum']], function ($router) {

    Route::get('/barang', [BarangController::class, 'getBarang']);

    // Access Control List (attr based)
    Route::get('/dashboard', function () {
        return 'API ini hanya bisa diakses oleh role admin';
    })->middleware(['auth:sanctum', 'role:admin']);

    // ACL (list based)
    Route::group(['middleware' => 'permission:create-barang'], function () {
        Route::post('/tambah-barang', [BarangController::class, 'tambah']);
    });

    Route::group(['middleware' => 'permission:edit-barang'], function () {
        Route::post('/edit-barang/{id}', [BarangController::class, 'edit']);
    });

    Route::group(['middleware' => 'permission:delete-barang'], function () {
        Route::post('/hapus-barang/{id}', [BarangController::class, 'hapus']);
    });
    
});

// API third-party
Route::get('/riwayat-transaksi', [testController::class, 'getTransaksi']);
// Route::post('/tambah-transaksi', [testController::class, 'tambahTransaksi']);

// XSS example
Route::post('/input-test', [testController::class, 'testInput']);


// Last activity middleware
Route::group(['middleware' => ['checkLastActivity']], function () {
});