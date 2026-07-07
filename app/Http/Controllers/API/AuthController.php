<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Login gagal, email atau password salah.'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'alamat' => ['nullable', 'string'],
            'no_ktp' => ['nullable', 'string', 'max:255', 'unique:users,no_ktp'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $lastPasien = User::where('role', 'pasien')->latest('id')->first();
        $number = 1;

        if ($lastPasien && preg_match('/RM(\d+)/', $lastPasien->no_rm, $matches)) {
            $number = intval($matches[1]) + 1;
        }

        $validated['role'] = 'pasien';
        $validated['no_rm'] = 'RM' . str_pad($number, 4, '0', STR_PAD_LEFT);
        $validated['password'] = $validated['password'];

        $user = User::create($validated);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
