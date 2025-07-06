<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplier = Supplier::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $supplier
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'pic_nama' => 'nullable|string|max:255',
            'pic_kontak' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $supplier = Supplier::create($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Supplier berhasil ditambahkan',
            'data' => $supplier
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::with(['detailPengeluaran.pengeluaran', 'inventaris'])
            ->find($id);
        
        if (!$supplier) {
            return response()->json([
                'status' => 'error',
                'message' => 'Supplier tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $supplier
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::find($id);
        
        if (!$supplier) {
            return response()->json([
                'status' => 'error',
                'message' => 'Supplier tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'pic_nama' => 'nullable|string|max:255',
            'pic_kontak' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $supplier->update($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Supplier berhasil diupdate',
            'data' => $supplier
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Supplier::find($id);
        
        if (!$supplier) {
            return response()->json([
                'status' => 'error',
                'message' => 'Supplier tidak ditemukan'
            ], 404);
        }

        // Check if supplier is used
        if ($supplier->detailPengeluaran()->count() > 0 || $supplier->inventaris()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Supplier tidak dapat dihapus karena masih digunakan'
            ], 400);
        }

        $supplier->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Supplier berhasil dihapus'
        ]);
    }
}
