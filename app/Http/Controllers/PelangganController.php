<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Validator;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pelanggan::query();
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $pelanggan = $query->orderBy('nama')->paginate(10);
        
        return response()->json([
            'status' => 'success',
            'data' => $pelanggan
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Form create pelanggan'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|unique:pelanggan,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $pelanggan = Pelanggan::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Pelanggan berhasil dibuat',
            'data' => $pelanggan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pelanggan = Pelanggan::with(['transaksi.detailTransaksi.layanan'])->find($id);
        
        if (!$pelanggan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pelanggan tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $pelanggan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pelanggan = Pelanggan::find($id);
        
        if (!$pelanggan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pelanggan tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $pelanggan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pelanggan = Pelanggan::find($id);
        
        if (!$pelanggan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pelanggan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|unique:pelanggan,email,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $pelanggan->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Pelanggan berhasil diupdate',
            'data' => $pelanggan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pelanggan = Pelanggan::find($id);
        
        if (!$pelanggan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pelanggan tidak ditemukan'
            ], 404);
        }

        // Check if pelanggan has transaksi
        if ($pelanggan->transaksi()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pelanggan tidak dapat dihapus karena masih memiliki transaksi'
            ], 400);
        }

        $pelanggan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pelanggan berhasil dihapus'
        ]);
    }
}
