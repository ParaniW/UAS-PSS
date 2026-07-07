<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayarans = Periksa::with(['daftarPoli.pasien', 'daftarPoli.jadwalPeriksa.dokter'])
            ->where(function ($query) {
                $query->whereNotNull('bukti_pembayaran')
                      ->orWhere('status_pembayaran', 'ditolak');
            })
            ->latest('tgl_pembayaran')
            ->get();

        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function konfirmasi($id)
    {
        $periksa = Periksa::findOrFail($id);

        if (!$periksa->bukti_pembayaran) {
            return back()->with('error', 'Bukti pembayaran belum ada.');
        }

        $periksa->update([
            'status_pembayaran' => 'lunas',
        ]);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function tolak($id)
    {
        $periksa = Periksa::findOrFail($id);

        if (!$periksa->bukti_pembayaran) {
            return back()->with('error', 'Bukti pembayaran tidak ditemukan.');
        }

        if (Storage::disk('public')->exists($periksa->bukti_pembayaran)) {
            Storage::disk('public')->delete($periksa->bukti_pembayaran);
        }

        $periksa->update([
            'bukti_pembayaran' => null,
            'status_pembayaran' => 'ditolak',
        ]);

        return back()->with('success', 'Bukti pembayaran ditolak. Pasien bisa upload ulang.');
    }
}