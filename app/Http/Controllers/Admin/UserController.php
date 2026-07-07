<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Poli;
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
        $polis = Poli::all();

        return view('admin.dokter.create', compact('polis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'no_ktp'   => 'nullable|string|max:255',
            'no_hp'    => 'nullable|string|max:255',
            'alamat'   => 'nullable|string|max:255',
            'id_poli'  => 'required|exists:poli,id',
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

        return redirect()->route('admin.dokter.index')->with('success', 'Data dokter berhasil ditambahkan');
    }

    public function edit($id)
    {
        $dokter = User::where('role', 'dokter')->findOrFail($id);
        $polis = Poli::all();

        return view('admin.dokter.edit', compact('dokter', 'polis'));
    }

    public function update(Request $request, $id)
    {
        $dokter = User::where('role', 'dokter')->findOrFail($id);

        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $dokter->id,
            'password' => 'nullable|string|min:6',
            'no_ktp'   => 'nullable|string|max:255',
            'no_hp'    => 'nullable|string|max:255',
            'alamat'   => 'nullable|string|max:255',
            'id_poli'  => 'required|exists:poli,id',
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

        return redirect()->route('admin.dokter.index')->with('success', 'Data dokter berhasil diupdate');
    }

    public function destroy($id)
    {
        $dokter = User::where('role', 'dokter')->findOrFail($id);
        $dokter->delete();

        return redirect()->route('admin.dokter.index')->with('success', 'Data dokter berhasil dihapus');
    }
}       