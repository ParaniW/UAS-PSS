<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('poli')->where('role', 'dokter');

        if ($search = $request->query('search')) {
            $query->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_poli')) {
            $query->where('id_poli', $request->query('id_poli'));
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
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'id_poli' => ['required', 'exists:poli,id'],
        ]);

        $data['role'] = 'dokter';
        $user = User::create($data);

        return response()->json($user->load('poli'), 201);
    }

    public function show(string $id)
    {
        $dokter = User::with('poli')->where('role', 'dokter')->findOrFail($id);

        return response()->json($dokter);
    }

    public function update(Request $request, string $id)
    {
        $dokter = User::where('role', 'dokter')->findOrFail($id);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($dokter->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'no_ktp' => ['nullable', 'string', 'max:255', Rule::unique('users', 'no_ktp')->ignore($dokter->id)],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'id_poli' => ['required', 'exists:poli,id'],
        ]);

        if ($request->filled('password')) {
            $dokter->password = $request->password;
        }

        $dokter->update(array_filter($data, fn ($value) => $value !== null));

        return response()->json($dokter->refresh()->load('poli'));
    }

    public function destroy(string $id)
    {
        $dokter = User::where('role', 'dokter')->findOrFail($id);
        $dokter->delete();

        return response()->json(['message' => 'Dokter berhasil dihapus']);
    }
}
