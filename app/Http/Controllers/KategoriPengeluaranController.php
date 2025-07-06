<?php

namespace App\Http\Controllers;

use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriPengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategori = KategoriPengeluaran::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $kategori
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kategori = KategoriPengeluaran::create($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Kategori pengeluaran berhasil ditambahkan',
            'data' => $kategori
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kategori = KategoriPengeluaran::with(['pengeluaran', 'inventaris'])
            ->find($id);
        
        if (!$kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori pengeluaran tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $kategori
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kategori = KategoriPengeluaran::find($id);
        
        if (!$kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori pengeluaran tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kategori->update($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Kategori pengeluaran berhasil diupdate',
            'data' => $kategori
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kategori = KategoriPengeluaran::find($id);
        
        if (!$kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori pengeluaran tidak ditemukan'
            ], 404);
        }

        // Check if category is used in pengeluaran
        if ($kategori->pengeluaran()->count() > 0 || $kategori->inventaris()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan'
            ], 400);
        }

        $kategori->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Kategori pengeluaran berhasil dihapus'
        ]);
    }
}
