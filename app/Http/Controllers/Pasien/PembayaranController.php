<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index()
    {
        $tagihans = Periksa::with(['daftarPoli.pasien', 'daftarPoli.jadwalPeriksa.dokter'])
            ->whereHas('daftarPoli', function ($query) {
                $query->where('id_pasien', Auth::id());
            })
            ->latest('tgl_periksa')
            ->get();

        return view('pasien.pembayaran.index', compact('tagihans'));
    }

    public function upload(Request $request, $id)
{
    $request->validate([
        'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $periksa = Periksa::findOrFail($id);

    try {
        $path = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');

        $periksa->update([
            'bukti_pembayaran' => $path,
            'status_pembayaran' => 'menunggu_verifikasi',
            'tgl_pembayaran' => now(),
        ]);

        return back()->with('success', 'Berhasil diupload! Status: ' . $periksa->status_pembayaran);
    } catch (\Exception $e) {
        // Jika ada error database, ini akan muncul di layar
        return back()->with('error', 'Gagal update database: ' . $e->getMessage());
    }
}
}