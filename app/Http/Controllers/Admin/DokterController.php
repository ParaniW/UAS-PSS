<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DokterController extends Controller
{
    public function index()
    {
        $dokters = User::with('poli')
            ->where('role', 'dokter')
            ->latest()
            ->get();

        return view('admin.dokter.index', compact('dokters'));
    }

    public function create()
    {
        $polis = Poli::orderBy('nama_poli')->get();

        return view('admin.dokter.create', compact('polis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'no_ktp'   => 'required|string|max:255|unique:users,no_ktp', // Validasi Unique KTP
            'no_hp'    => 'nullable|string|max:255',
            'alamat'   => 'nullable|string|max:255',
            'id_poli'  => 'required|exists:poli,id',
        ], [
            // Pesan error bahasa Indonesia
            'no_ktp.unique' => 'Nomor KTP sudah terdaftar.',
            'email.unique'  => 'Email sudah digunakan.',
        ]);

        User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'no_ktp'   => $request->no_ktp,
            'no_hp'    => $request->no_hp,
            'alamat'   => $request->alamat,
            'id_poli'  => $request->id_poli,
            'role'     => 'dokter',
        ]);

        return redirect()->route('admin.dokter.index')
            ->with('success', 'Data dokter berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $dokter = User::with('poli')
            ->where('role', 'dokter')
            ->findOrFail($id);

        return view('admin.dokter.show', compact('dokter'));
    }

    public function edit(string $id)
    {
        $dokter = User::where('role', 'dokter')->findOrFail($id);
        $polis = Poli::orderBy('nama_poli')->get();

        return view('admin.dokter.edit', compact('dokter', 'polis'));
    }

    public function update(Request $request, string $id)
    {
        $dokter = User::where('role', 'dokter')->findOrFail($id);

        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $dokter->id,
            'password' => 'nullable|string|min:6',
            'no_ktp'   => 'required|string|max:255|unique:users,no_ktp,' . $dokter->id, // Kecualikan KTP sendiri saat update
            'no_hp'    => 'nullable|string|max:255',
            'alamat'   => 'nullable|string|max:255',
            'id_poli'  => 'required|exists:poli,id',
        ], [
            'no_ktp.unique' => 'Nomor KTP sudah terdaftar.',
            'email.unique'  => 'Email sudah digunakan.',
        ]);

        $data = [
            'nama'    => $request->nama,
            'email'   => $request->email,
            'no_ktp'  => $request->no_ktp,
            'no_hp'   => $request->no_hp,
            'alamat'  => $request->alamat,
            'id_poli' => $request->id_poli,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $dokter->update($data);

        return redirect()->route('admin.dokter.index')
            ->with('success', 'Data dokter berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $dokter = User::where('role', 'dokter')->findOrFail($id);
        $dokter->delete();

        return redirect()->route('admin.dokter.index')
            ->with('success', 'Data dokter berhasil dihapus');
    }
}