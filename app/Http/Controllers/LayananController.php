<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Layanan;
use Illuminate\Support\Facades\Validator;

class LayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Layanan::query();
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_layanan', 'like', "%{$search}%");
        }
        
        // Filter by satuan
        if ($request->has('satuan')) {
            $query->where('satuan', $request->satuan);
        }
        
        $layanan = $query->orderBy('nama_layanan')->paginate(10);
        
        return response()->json([
            'status' => 'success',
            'data' => $layanan
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Form create layanan',
            'satuan_options' => ['KG', 'PCS', 'M']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_layanan' => 'required|string|max:255',
            'satuan' => 'required|in:KG,PCS,M',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $layanan = Layanan::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Layanan berhasil dibuat',
            'data' => $layanan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $layanan = Layanan::with(['detailTransaksi.transaksi.pelanggan'])->find($id);
        
        if (!$layanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $layanan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $layanan = Layanan::find($id);
        
        if (!$layanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $layanan,
            'satuan_options' => ['KG', 'PCS', 'M']
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $layanan = Layanan::find($id);
        
        if (!$layanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_layanan' => 'required|string|max:255',
            'satuan' => 'required|in:KG,PCS,M',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $layanan->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Layanan berhasil diupdate',
            'data' => $layanan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $layanan = Layanan::find($id);
        
        if (!$layanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan tidak ditemukan'
            ], 404);
        }

        // Check if layanan has detail transaksi
        if ($layanan->detailTransaksi()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan tidak dapat dihapus karena masih digunakan dalam transaksi'
            ], 400);
        }

        $layanan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Layanan berhasil dihapus'
        ]);
    }
}
