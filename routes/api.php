<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DokumenController;
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

Route::group(['middleware' => 'auth:sanctum'], function ($router) {
    Route::post('/barang', [BarangController::class, 'getBarang']);
});

Route::prefix('auth')->group(function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [RegisteredUserController::class, 'store']);
});


// File
Route::prefix('dokumen')->group(function ($router) {
    Route::post('/simpan', [DokumenController::class, 'simpan']); // contoh enkripsi file
    // Route::get('/download', [DokumenController::class, 'downloadFile']); // path traversal validation
    Route::get('/download', [DokumenController::class, 'download']); // path traversal vulnerable
});