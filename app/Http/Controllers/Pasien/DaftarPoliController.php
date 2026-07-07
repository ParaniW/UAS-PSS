<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarPoliController extends Controller
{
    public function get()
    {
        $user = Auth::user();
        $polis = Poli::all();
        $jadwals = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

        return view('pasien.daftar-poli', compact(
            'user',
            'polis',
            'jadwals'
        ));
    }

    public function submit(Request $request)
{
    $request->validate([
        'id_jadwal' => 'required|exists:jadwal_periksa,id',
        'keluhan'   => 'required'
    ]);

    // Nomor antrian otomatis
    $noAntrian = DaftarPoli::where('id_jadwal', $request->id_jadwal)->count() + 1;

    DaftarPoli::create([
        'id_pasien'  => Auth::id(),
        'id_jadwal'  => $request->id_jadwal,
        'keluhan'    => $request->keluhan,
        'no_antrian' => $noAntrian,
    ]);

    return redirect()->route('pasien.riwayat-pendaftaran.index')
        ->with('message', 'Pendaftaran berhasil')
        ->with('type', 'success');
}

    // Tambahan agar route create tetap berjalan
    public function create()
    {
        return $this->get();
    }

    // Tambahan agar route store tetap berjalan
    public function store(Request $request)
    {
        return $this->submit($request);
    }
}
