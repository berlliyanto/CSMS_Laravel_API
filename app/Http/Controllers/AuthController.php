<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah'],
            ]);
        }

        $token = $user->createToken($user->username . "_token")->plainTextToken;

        return [
            'token' => $token
        ];
    }

    public function logout(Request $request)
    {   
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "message" => "Berhasil logout"
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            "name" => "required",
            "username" => "required",
            "email" => "required|email",
            "password" => "required",
        ]);

        $register = User::create([
            "name" => $request->name,
            "username" => $request->username,
            "email" => $request->email,
            "password" => $request->password,
            "role_id" => 6
        ]);

        if(!$register){
            return response()->json([
                "message" => "Gagal daftar akun"
            ],400);
        }

        return response()->json([
            "message" => "Berhasil daftar akun",
            "data" => $register
        ],200);
    }

    public function profile()
    {
        $user = Auth::user();
        $userData = User::with('role:role_name,id')->where('id', $user->id)->get()->first();
        return response()->json([
            "message" => "Berhasil ambil profile",
            "data" => $userData,
        ]);
    }
}
