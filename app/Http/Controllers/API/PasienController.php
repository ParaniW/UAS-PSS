<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'pasien');

        if ($search = $request->query('search')) {
            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_rm', 'like', "%{$search}%");
            });
        }

        $sortBy = in_array($request->query('order_by'), ['nama', 'email', 'created_at'])
            ? $request->query('order_by')
            : 'created_at';
        $sortDirection = strtolower($request->query('order_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));

        return $this->paginateResponse($query->orderBy($sortBy, $sortDirection)->paginate($perPage));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'no_ktp' => ['nullable', 'string', 'max:255', 'unique:users,no_ktp'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $lastPasien = User::where('role', 'pasien')->latest('id')->first();
        $number = 1;

        if ($lastPasien && preg_match('/RM(\d+)/', $lastPasien->no_rm, $matches)) {
            $number = intval($matches[1]) + 1;
        }

        $data['role'] = 'pasien';
        $data['no_rm'] = 'RM' . str_pad($number, 4, '0', STR_PAD_LEFT);

        $pasien = User::create($data);

        return response()->json($pasien, 201);
    }

    public function show(string $id)
    {
        $pasien = User::where('role', 'pasien')->findOrFail($id);

        return response()->json($pasien);
    }

    public function update(Request $request, string $id)
    {
        $pasien = User::where('role', 'pasien')->findOrFail($id);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($pasien->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'no_ktp' => ['nullable', 'string', 'max:255', Rule::unique('users', 'no_ktp')->ignore($pasien->id)],
            'alamat' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        if ($request->filled('password')) {
            $pasien->password = $request->password;
        }

        $pasien->update(array_filter($data, fn ($value) => $value !== null));

        return response()->json($pasien->refresh());
    }

    public function destroy(string $id)
    {
        $pasien = User::where('role', 'pasien')->findOrFail($id);
        $pasien->delete();

        return response()->json(['message' => 'Pasien berhasil dihapus']);
    }
}
