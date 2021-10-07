<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Function register
    public function register(Request $request){
        try {
            // Validasi data yang diinput untuk register
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['nullable', 'string', 'max:255'],
                'password' => ['required', 'string', new Password],
            ]);

            // Membuat data user menggunakan Models User
            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();
            
            // Membuat token ketika berhasil register, dan akan digunakan untuk login langsung ketika berhasil register (Menggunakan fungsi Laravel Jetstream)
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Jika berhasil akan mengembalikan nilai token, type_token, dan usernya, serta message
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');
        } catch (Exception $error){
            // Jika gagal maka akan mengembalikan pesan, serta error yang ada
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    // Function Login
    public function login(Request $request){
        try {
            // Validasi data yang diinput untuk login
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']); // Menampung isi credentials login ke dalam variabel

            // Pengecekan login, jika gagal maka akan menampilkan pesan seperti di bawah menggunakan fungsi Auth::attempt()
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->first();

            // Pengecekan password, jika password salah maka akan diarahkan seperti perintah di bawah
            if(! Hash::check($request->password, $user->password, [])){
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Jika berhasil login maka akan memberikan data berupa token dan user yang login
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

            // Jika gagal maka akan mengembalikan berupa pesan error yang ada
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    // Function UserProfile
    public function fetch(Request $request){
        return ResponseFormatter::success($request->user(), 'Data profile user berhasil diambil');
    }

    // Function updateProfile
    public function updateProfile(Request $request){
        $data = $request->all();

        $user = Auth::user(); // Mengambil data user yang sedang login

        $user->update($data); // Mengupdate data user dari data yang ada

        return ResponseFormatter::success($user, 'Profile Updated');
    }

    // Function logout
    public function logout(Request $request){
        // Merevoke token menggunakan fungsi dari Laravel
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }
}
