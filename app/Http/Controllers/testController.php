<?php

namespace App\Http\Controllers;

use App\Models\logServiceModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;

class testController extends Controller
{
    protected $encrypter;

    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    public function encryptData($data)
    {
        return $this->encrypter->encrypt($data);
    }

    public function decryptData($encryptedData)
    {
        try {
            return $this->encrypter->decrypt($encryptedData);
        } catch (DecryptException $e) {
            // Handle error ketika gagal mendekripsi data
            return null;
        }
    }

    public function exampleApiMethod(Request $request)
    {
        $param = $request->all();

        // Mengenkripsi data
        $encryptedParam = $this->encryptData($param);
        // dd($encryptedParam);

        // Mengirimkan data yang dienkripsi ke API

        // Menerima data yang dienkripsi dari API
        
        // Mendekripsi data
        $decryptedParam = $this->decryptData($encryptedParam);
        // dd($decryptedParam);

        // Menggunakan data yang telah didekripsi

        // $data = User::select('name', 'email')->where('name', $decryptedParam['nama'])->first();
        // return response()->json(['data' => $data], 200);
    }

    public function getTransaksi (Request $request) 
    {
        $data = $this->curl_get(env('THIRD_API_URL', 'http://localhost:8080/api/') . 'daftar-transaksi');
        $status = $data['code'];
        $data = json_decode($data['response'], true);

        $response = array(
            'success' => true,
            'response_code' => $status,
            'response' => $data
        );

        // input ke log service
        $paramsLog = array(
            'id' => $this->uuid(),
            'ip_address' => $request->ip(),
            'service_name' => 'get daftar transaksi',
            'response' => json_encode($data),
            'parameter' => '',
            'keterangan' => $status == 200 ? 'berhasil' : 'gagal',
            'date_time' => Carbon::now()->setTimezone('Asia/Jakarta')
        );
        // dd($paramsLog); 
        
        $log = logServiceModel::create($paramsLog);


        return response()->json($response, $status);
    }

    public function testInput(Request $request) 
    {
        $nama = $request->input('nama'); // gunakan fungsi e() atau strip_tags() untuk escape html character
        // dd($nama);
        // $response = "Halo, " . $nama; 
        $response = $nama;
        return response()->json(['message' => $response]);
    }

    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx|max:2048' // Validasi tipe file dan ukuran maksimal
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400); // Mengirimkan response error jika validasi gagal
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName(); 
    
            // Membaca isi file
            $content = file_get_contents($file->getRealPath());
            $validasi_content = strtolower($content);

            // Melakukan validasi terhadap adanya script HTML atau JavaScript
            if ( preg_match( '/<script\b[^>]*>(.*?)<\/script>/is', $content ) ) 
            {
                return response()->json(['error' => 'File berisi script HTML atau JavaScript'], 400);
            }
            elseif ( strpos( $validasi_content, 'script' ) !== FALSE)
            {
                return response()->json(['error' => 'File berisi script HTML atau JavaScript'], 400);
            }
            elseif ( strpos( $validasi_content, 'javascript' ) !== FALSE)
            {
                return response()->json(['error' => 'File berisi script HTML atau JavaScript'], 400);
            }
            elseif ( strpos( $validasi_content, 'alert' ) !== FALSE)
            {
                return response()->json(['error' => 'File berisi script HTML atau JavaScript'], 400);
            }

            // upload
            $file->move('public/files', $fileName);
    
            
    
            return response()->json(['message' => 'File berhasil diunggah']);
        }
    }

    public function tambahTransaksi (Request $request) 
    {
        $param = $request->all();
        // Mengenkripsi data
        $encryptedParam = $this->encryptData($param);
        $postData = array(
            'params' => $encryptedParam
        );
        // $postData = $param;
        // dd($postData);
        
        $data = $this->curl_post_request(env('THIRD_API_URL', 'http://localhost:8080/api/') . 'tambah-transaksi', $postData);
        $status = $data['code'];
        $data = json_decode($data['response'], true);

        $response = array(
            'success' => true,
            'response_code' => $status,
            'response' => $data
        );

        // input ke log service
        $paramsLog = array(
            'id' => $this->uuid(),
            'ip_address' => $request->ip(),
            'service_name' => 'tambah daftar transaksi',
            'response' => json_encode($data),
            'parameter' => '',
            'keterangan' => $status == 200 ? 'berhasil' : 'gagal',
            'date_time' => Carbon::now()->setTimezone('Asia/Jakarta')
        );
        // dd($paramsLog); 
        
        $log = logServiceModel::create($paramsLog);


        return response()->json($response, $status);

    }

    protected function curl_get($urlRequest, $headers = array())
    {
        $curl = curl_init($urlRequest);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_URL, $urlRequest);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $out = ['code' => $status_code, 'response' => $response];
        return $out;
    }

    protected function curl_post_request($urlRequest, $postData, $headers = array())
    {
        $curl = curl_init($urlRequest);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_URL, $urlRequest);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_SSLVERSION, 6);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) curlrome/24.0.1312.52 Safari/537.17');
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $out = ['code' => $status_code, 'response' => $response];
        return $out;
    }

    protected function uuid() 
    {
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
