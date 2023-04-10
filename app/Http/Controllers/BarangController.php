<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function getBarang(Request $request) {
        $barang = BarangModel::all();

        return response()->json(['success' => true, 'data' => $barang], 200);
    }
}
