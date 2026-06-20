<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengeluaran::query();

        if ($request->has('dari_tanggal') && $request->has('sampai_tanggal')) {
            $query->whereBetween('date', [$request->dari_tanggal, $request->sampai_tanggal]);
        }

        $pengeluaran = $query->orderBy('date', 'desc')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $pengeluaran
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pengeluaran = Pengeluaran::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran berhasil ditambahkan',
            'data' => $pengeluaran
        ], 201);
    }

    public function show(string $id)
    {
        $pengeluaran = Pengeluaran::find($id);

        if (!$pengeluaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengeluaran tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $pengeluaran
        ]);
    }

    public function update(Request $request, string $id)
    {
        $pengeluaran = Pengeluaran::find($id);
        if (!$pengeluaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengeluaran tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pengeluaran->update([
            'name' => $request->name,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran berhasil diupdate',
            'data' => $pengeluaran
        ]);
    }

    public function destroy(string $id)
    {
        $pengeluaran = Pengeluaran::find($id);
        if (!$pengeluaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengeluaran tidak ditemukan'
            ], 404);
        }

        $pengeluaran->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran berhasil dihapus'
        ]);
    }
}
