<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Stevebauman\Location\Facades\Location;
use Torann\GeoIP\Facades\GeoIP;

class AuthController extends Controller
{
    public function login (Request $request) {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user)
        {
            return response()->json(['message'=>'email/password salah!'], 400);
        }
        
        if (!Hash::check($request->password, $user->password))
        {
            return response()->json(['message'=>'email/password salah!'], 400);
        }
        
        if ($user->logged_in != 0)
        {
            return response()->json(['message'=>'pengguna sudah login'], 400);
        }

        // update status login
        User::where('email', $request->email)->update(['logged_in' => 1]);

        // IP Location tidak bekerja di Localhost
        // USA IP Address
        // $ip = '66.102.0.0';
        // IND IP Address
        $ip = '101.0.4.0';

        $location = Location::get($ip);

        LoginActivity::create([
            'user_id' => $user->id,
            'username' => $user->name,
            'ip_address' => $ip, // $request->ip()
            'browser' => $request->header('User-Agent'),
            'os' => $this->getUserOperatingSystem($request->header('User-Agent')),
            'login_time' => Carbon::now()->setTimezone($location->timezone),
            'location' => $location->countryName,
            'city_name' => $location->cityName,
            'timezone' => $location->timezone
        ]);

        $token = $user->createToken('access_token');
        
        return response()->json([
            'token' => $token->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete(); // Menghapus token saat logout
        User::where('id', $request->user()->id)->update(['logged_in' => 0]);
        return response()->json(['message' => 'Successfully logged out']);
    }


    private function getUserOperatingSystem($userAgent)
    {
        $agent = new \Jenssegers\Agent\Agent;
        $agent->setUserAgent($userAgent);
        return $agent->platform();
    }
}
