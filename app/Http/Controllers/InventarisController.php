<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\KategoriPengeluaran;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventarisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Inventaris::with(['kategori', 'supplier']);
        
        // Filter by category
        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        
        // Filter by supplier
        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        // Filter by stock status
        if ($request->has('status_stok') && $request->status_stok === 'low') {
            $query->whereRaw('jumlah_stok <= minimal_stok');
        }
        
        $inventaris = $query->orderBy('nama_barang')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $inventaris
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategori_pengeluaran,id',
            'jumlah_stok' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'harga_beli_terakhir' => 'nullable|numeric|min:0',
            'minimal_stok' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:supplier,id',
            'tanggal_beli_terakhir' => 'nullable|date',
            'lokasi_penyimpanan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $inventaris = Inventaris::create($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Inventaris berhasil ditambahkan',
            'data' => $inventaris->load(['kategori', 'supplier'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $inventaris = Inventaris::with(['kategori', 'supplier'])
            ->find($id);
        
        if (!$inventaris) {
            return response()->json([
                'status' => 'error',
                'message' => 'Inventaris tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $inventaris
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $inventaris = Inventaris::find($id);
        
        if (!$inventaris) {
            return response()->json([
                'status' => 'error',
                'message' => 'Inventaris tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategori_pengeluaran,id',
            'jumlah_stok' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'harga_beli_terakhir' => 'nullable|numeric|min:0',
            'minimal_stok' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:supplier,id',
            'tanggal_beli_terakhir' => 'nullable|date',
            'lokasi_penyimpanan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $inventaris->update($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Inventaris berhasil diupdate',
            'data' => $inventaris->load(['kategori', 'supplier'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $inventaris = Inventaris::find($id);
        
        if (!$inventaris) {
            return response()->json([
                'status' => 'error',
                'message' => 'Inventaris tidak ditemukan'
            ], 404);
        }

        $inventaris->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Inventaris berhasil dihapus'
        ]);
    }
    
    /**
     * Get low stock items
     */
    public function getLowStockItems()
    {
        $lowStock = Inventaris::with(['kategori', 'supplier'])
            ->whereRaw('jumlah_stok <= minimal_stok')
            ->orderBy('jumlah_stok')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $lowStock
        ]);
    }
}
