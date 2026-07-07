<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PoliController extends Controller
{
    public function index(Request $request)
    {
        $query = Poli::query();

        if ($search = $request->query('search')) {
            $query->where('nama_poli', 'like', "%{$search}%")
                ->orWhere('kode_poli', 'like', "%{$search}%");
        }

        $sortBy = in_array($request->query('order_by'), ['nama_poli', 'kode_poli', 'tarif', 'created_at'])
            ? $request->query('order_by')
            : 'created_at';
        $sortDirection = strtolower($request->query('order_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));

        return $this->paginateResponse($query->orderBy($sortBy, $sortDirection)->paginate($perPage));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_poli' => ['required', 'string', 'max:25'],
            'keterangan' => ['nullable', 'string'],
            'tarif' => ['required', 'integer', 'min:0'],
        ]);

        $data['kode_poli'] = Poli::count() >= 26
            ? 'P' . str_pad(Poli::count() + 1, 3, '0', STR_PAD_LEFT)
            : chr(65 + Poli::count());

        $poli = Poli::create($data);

        return response()->json($poli, 201);
    }

    public function show(string $id)
    {
        return response()->json(Poli::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $poli = Poli::findOrFail($id);

        $data = $request->validate([
            'nama_poli' => ['required', 'string', 'max:25'],
            'keterangan' => ['nullable', 'string'],
            'tarif' => ['required', 'integer', 'min:0'],
        ]);

        $poli->update($data);

        return response()->json($poli);
    }

    public function destroy(string $id)
    {
        $poli = Poli::findOrFail($id);
        $poli->delete();

        return response()->json(['message' => 'Poli berhasil dihapus']);
    }
}
