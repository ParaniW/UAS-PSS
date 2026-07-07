<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use Illuminate\Http\Request;

class PoliController extends Controller
{
    public function index()
    {
        $polis = Poli::latest()->get();
        return view('admin.polis.index', compact('polis'));
    }

    public function create()
    {
        return view('admin.polis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_poli'  => 'required|string|max:25',
            'keterangan' => 'nullable|string',
            'tarif'      => 'required|integer|min:0',
        ]);

        // hitung jumlah poli yang sudah ada
        $totalPoli = Poli::count();

        // generate kode otomatis (A, B, C, D...)
        $kodePoli = chr(65 + $totalPoli);

        // masukkan kode ke data
        $validated['kode_poli'] = $kodePoli;

        Poli::create($validated);

        return redirect()
            ->route('admin.polis.index')
            ->with('success', 'Poli berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $poli = Poli::findOrFail($id);
        return view('admin.polis.show', compact('poli'));
    }

    public function edit(string $id)
    {
        $poli = Poli::findOrFail($id);
        return view('admin.polis.edit', compact('poli'));
    }
public function get()
{
    $user = Auth::user(); // Ini mengambil data user login (termasuk no_rm)
    $polis = Poli::all();
    $jadwal = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

    return view('pasien.daftar', [
        'user' => $user, // Variabel ini yang dipakai di Blade tadi
        'polis' => $polis,
        'jadwals' => $jadwal,
    ]);
}   

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama_poli'  => 'required|string|max:25',
            'keterangan' => 'nullable|string',
            'tarif'      => 'required|integer|min:0',
        ]);

        $poli = Poli::findOrFail($id);
        $poli->update($validated);

        return redirect()
            ->route('admin.polis.index')
            ->with('success', 'Poli berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $poli = Poli::findOrFail($id);
        $poli->delete();

        return redirect()
            ->route('admin.polis.index')
            ->with('success', 'Poli berhasil dihapus');
    }
}