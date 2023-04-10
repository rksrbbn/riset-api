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

    public function tambah(Request $request) {
        $paramsData = array(
            'nama_barang' => $request->input('nama_barang'),
            'harga_barang' => $request->input('harga_barang')
        );

        $tambah = BarangModel::create($paramsData);
        return response()->json(['succcess' => true, 'message' => 'Berhasil Tambah Barang'], 200);
    }

    public function edit(Request $request, $id) {
        $paramsData = array(
            'nama_barang' => $request->input('nama_barang'),
            'harga_barang' => $request->input('harga_barang')
        );

        $barang = BarangModel::findOrFail($id);
        $edit = $barang->update($paramsData);

        return response()->json(['succcess' => true, 'message' => 'Berhasil edit Barang'], 200);
    }

    public function hapus(Request $request, $id) {
        
        $hapus = BarangModel::where('id', $id)->delete();
        return response()->json(['succcess' => true, 'message' => 'Berhasil hapus Barang'], 200);
    }
}
