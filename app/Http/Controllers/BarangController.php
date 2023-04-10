<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\LogAktivitas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    
    public function getBarang(Request $request) {
        $user = $request->user();
        
        $barang = BarangModel::all();

        $paramsLog = array(
            'id' => $this->uuid(),
            'username' => $user->name,
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'nama_aktivitas' => 'melihat data barang',
            'keterangan' => 'user melihat data barang',
            'data_sebelum' => null,
            'data_sesudah' => null,
            'date_time' => Carbon::now()->setTimezone('Asia/Jakarta'),
            'browser' => $request->header('User-Agent')
        );

        $log = LogAktivitas::create($paramsLog);
        return response()->json(['success' => true, 'data' => $barang], 200);
    }

    public function tambah(Request $request) {
        $user = $request->user();

        $paramsData = array(
            'nama_barang' => $request->input('nama_barang'),
            'harga_barang' => $request->input('harga_barang')
        );
        
        $tambah = BarangModel::create($paramsData);

        $paramsLog = array(
            'id' => $this->uuid(),
            'username' => $user->name,
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'nama_aktivitas' => 'menambah data barang',
            'keterangan' => 'user menambah data barang',
            'data_sebelum' => null,
            'data_sesudah' => $tambah,
            'date_time' => Carbon::now()->setTimezone('Asia/Jakarta'),
            'browser' => $request->header('User-Agent')
        );

        $log = LogAktivitas::create($paramsLog);
        return response()->json(['succcess' => true, 'message' => 'Berhasil Tambah Barang'], 200);
    }

    public function edit(Request $request, $id) {
        $user = $request->user();

        $paramsData = array(
            'nama_barang' => $request->input('nama_barang'),
            'harga_barang' => $request->input('harga_barang')
        );

        $barang = BarangModel::findOrFail($id);
        $dataSebelum = clone $barang;

        $columnsHidden = $barang->getHidden();
        foreach ($columnsHidden as $key) 
        {                    
            $barang->makeVisible($key);
        }
        
        foreach($paramsData as $pk => $pv )
        {
            $barang->$pk = $pv;
        }

        $dataSesudah = $barang;
        $edit = $barang->save();

        $paramsLog = array(
            'id' => $this->uuid(),
            'username' => $user->name,
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'nama_aktivitas' => 'mengubah data barang',
            'keterangan' => 'user mengubah data barang',
            'data_sebelum' => $dataSebelum,
            'data_sesudah' => $dataSesudah,
            'date_time' => Carbon::now()->setTimezone('Asia/Jakarta'),
            'browser' => $request->header('User-Agent')
        );

        $log = LogAktivitas::create($paramsLog);

        return response()->json(['succcess' => true, 'message' => 'Berhasil edit Barang'], 200);
    }

    public function hapus(Request $request, $id) {
        $user = $request->user();
        
        $barang = BarangModel::where('id', $id);
        $dataSebelum = clone $barang;
        $hapus = $barang->delete();

        $paramsLog = array(
            'id' => $this->uuid(),
            'username' => $user->name,
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'nama_aktivitas' => 'menghapus data barang',
            'keterangan' => 'user menghapus data barang',
            'data_sebelum' => $dataSebelum->first(),
            'data_sesudah' => null,
            'date_time' => Carbon::now()->setTimezone('Asia/Jakarta'),
            'browser' => $request->header('User-Agent')
        );

        $log = LogAktivitas::create($paramsLog);
        return response()->json(['succcess' => true, 'message' => 'Berhasil hapus Barang'], 200);
    }

    protected function uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}
