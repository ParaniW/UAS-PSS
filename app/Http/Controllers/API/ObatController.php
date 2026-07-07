<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $query = Obat::query();

        if ($search = $request->query('search')) {
            $query->where('nama_obat', 'like', "%{$search}%")
                ->orWhere('kemasan', 'like', "%{$search}%");
        }

        $sortBy = in_array($request->query('order_by'), ['nama_obat', 'harga', 'stok', 'created_at'])
            ? $request->query('order_by')
            : 'created_at';
        $sortDirection = strtolower($request->query('order_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));

        return $this->paginateResponse($query->orderBy($sortBy, $sortDirection)->paginate($perPage));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_obat' => ['required', 'string', 'max:255'],
            'kemasan' => ['nullable', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
        ]);

        $obat = Obat::create($data);

        return response()->json($obat, 201);
    }

    public function show(string $id)
    {
        return response()->json(Obat::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $obat = Obat::findOrFail($id);

        $data = $request->validate([
            'nama_obat' => ['required', 'string', 'max:255'],
            'kemasan' => ['nullable', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
        ]);

        $obat->update($data);

        return response()->json($obat);
    }

    public function destroy(string $id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return response()->json(['message' => 'Obat berhasil dihapus']);
    }
}
