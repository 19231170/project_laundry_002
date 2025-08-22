<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\DetailPengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\Supplier;
use App\Models\Inventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengeluaran::with(['kategori', 'user', 'detailPengeluaran.supplier']);
        
        // Filter by date range
        if ($request->has('dari_tanggal') && $request->has('sampai_tanggal')) {
            $query->whereBetween('tanggal', [$request->dari_tanggal, $request->sampai_tanggal]);
        }
        
        // Filter by category
        if ($request->has('kategori_id')) {
            $query->where('kategori_pengeluaran_id', $request->kategori_id);
        }
        
        $pengeluaran = $query->orderBy('tanggal', 'desc')->paginate(10);
        
        return response()->json([
            'status' => 'success',
            'data' => $pengeluaran
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
            'supplier_id' => 'nullable|exists:supplier,id',
            'penerima' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
            'detail_pengeluaran' => 'required|array',
            'detail_pengeluaran.*.nama' => 'required|string',
            'detail_pengeluaran.*.qty' => 'required|numeric|min:1',
            'detail_pengeluaran.*.harga' => 'required|numeric|min:0',
            'jumlah_total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Hitung total biaya (gunakan nilai yang diberikan atau hitung ulang)
            $totalBiaya = $request->jumlah_total ?? 0;
            
            if ($totalBiaya == 0) {
                foreach ($request->detail_pengeluaran as $detail) {
                    $totalBiaya += $detail['qty'] * $detail['harga'];
                }
            }
            
            // Buat pengeluaran
            $pengeluaran = Pengeluaran::create([
                'tanggal' => $request->tanggal,
                'total_biaya' => $totalBiaya,
                'kategori_pengeluaran_id' => $request->kategori_pengeluaran_id,
                'supplier_id' => $request->supplier_id,
                'penerima' => $request->penerima,
                'keterangan' => $request->keterangan,
                'bukti_pembayaran' => null, // Bukti pembayaran diatur melalui fitur terpisah
                'user_id' => auth()->id() ?? 1, // Fallback jika tidak ada auth
            ]);
            
            // Simpan detail pengeluaran
            foreach ($request->detail_pengeluaran as $detail) {
                DetailPengeluaran::create([
                    'pengeluaran_id' => $pengeluaran->id,
                    'nama_item' => $detail['nama'],
                    'jumlah' => $detail['qty'],
                    'satuan' => 'pcs', // Default satuan
                    'harga_satuan' => $detail['harga'],
                    'subtotal' => $detail['qty'] * $detail['harga'],
                    'supplier_id' => $request->supplier_id, // Ambil dari pengeluaran
                ]);
                
                // Update stok inventaris jika kategori terkait dengan inventaris
                $kategori = KategoriPengeluaran::find($request->kategori_pengeluaran_id);
                if ($kategori && $kategori->terkait_inventaris && $request->supplier_id) {
                    // Coba cari inventaris yang sesuai
                    $inventaris = Inventaris::where('nama', 'like', '%' . $detail['nama'] . '%')->first();
                    
                    if ($inventaris) {
                        $inventaris->jumlah_stok += $detail['qty'];
                        $inventaris->harga_beli_terakhir = $detail['harga'];
                        $inventaris->tanggal_beli_terakhir = $request->tanggal;
                        $inventaris->supplier_id = $request->supplier_id;
                        $inventaris->save();
                    } else {
                        // Jika tidak ada, buat inventaris baru
                        Inventaris::create([
                            'nama' => $detail['nama'],
                            'jumlah_stok' => $detail['qty'],
                            'satuan' => 'pcs',
                            'harga_beli_terakhir' => $detail['harga'],
                            'tanggal_beli_terakhir' => $request->tanggal,
                            'supplier_id' => $request->supplier_id,
                            'kategori_id' => $request->kategori_pengeluaran_id
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pengeluaran berhasil ditambahkan',
                'data' => $pengeluaran->load(['kategori', 'detailPengeluaran.supplier'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Tidak perlu hapus file karena tidak ada upload file
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan pengeluaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengeluaran = Pengeluaran::with(['kategori', 'user', 'detailPengeluaran.supplier'])
            ->find($id);
        
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

    /**
     * Update the specified resource in storage.
     */
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
            'tanggal' => 'required|date',
            'kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
            'keterangan' => 'nullable|string',
            'bukti_pembayaran' => 'nullable|image|max:2048', // max 2MB
            'detail' => 'required|array',
            'detail.*.id' => 'nullable|exists:detail_pengeluaran,id',
            'detail.*.nama_item' => 'required|string',
            'detail.*.jumlah' => 'required|numeric|min:0.01',
            'detail.*.satuan' => 'required|string',
            'detail.*.harga_satuan' => 'required|numeric|min:0',
            'detail.*.supplier_id' => 'nullable|exists:supplier,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Upload bukti pembayaran jika ada
            if ($request->hasFile('bukti_pembayaran')) {
                // Hapus bukti lama jika ada
                if ($pengeluaran->bukti_pembayaran) {
                    Storage::disk('public')->delete($pengeluaran->bukti_pembayaran);
                }
                
                $buktiPath = $request->file('bukti_pembayaran')->store('bukti_pengeluaran', 'public');
                $pengeluaran->bukti_pembayaran = $buktiPath;
            }
            
            // Update basic info
            $pengeluaran->tanggal = $request->tanggal;
            $pengeluaran->kategori_pengeluaran_id = $request->kategori_pengeluaran_id;
            $pengeluaran->keterangan = $request->keterangan;
            
            // Update detail dan hitung total baru
            $totalBiaya = 0;
            $currentDetailIds = [];
            
            foreach ($request->detail as $detail) {
                if (isset($detail['id'])) {
                    // Update existing detail
                    $detailPengeluaran = DetailPengeluaran::find($detail['id']);
                    if ($detailPengeluaran && $detailPengeluaran->pengeluaran_id == $pengeluaran->id) {
                        $detailPengeluaran->update([
                            'nama_item' => $detail['nama_item'],
                            'jumlah' => $detail['jumlah'],
                            'satuan' => $detail['satuan'],
                            'harga_satuan' => $detail['harga_satuan'],
                            'subtotal' => $detail['jumlah'] * $detail['harga_satuan'],
                            'supplier_id' => $detail['supplier_id'] ?? null,
                        ]);
                        
                        $currentDetailIds[] = $detailPengeluaran->id;
                        $totalBiaya += $detailPengeluaran->subtotal;
                    }
                } else {
                    // Create new detail
                    $detailPengeluaran = DetailPengeluaran::create([
                        'pengeluaran_id' => $pengeluaran->id,
                        'nama_item' => $detail['nama_item'],
                        'jumlah' => $detail['jumlah'],
                        'satuan' => $detail['satuan'],
                        'harga_satuan' => $detail['harga_satuan'],
                        'subtotal' => $detail['jumlah'] * $detail['harga_satuan'],
                        'supplier_id' => $detail['supplier_id'] ?? null,
                    ]);
                    
                    $currentDetailIds[] = $detailPengeluaran->id;
                    $totalBiaya += $detailPengeluaran->subtotal;
                    
                    // Update stok inventaris jika ada
                    if (isset($detail['inventaris_id'])) {
                        $inventaris = Inventaris::find($detail['inventaris_id']);
                        if ($inventaris) {
                            $inventaris->jumlah_stok += $detail['jumlah'];
                            $inventaris->harga_beli_terakhir = $detail['harga_satuan'];
                            $inventaris->tanggal_beli_terakhir = $request->tanggal;
                            $inventaris->supplier_id = $detail['supplier_id'] ?? $inventaris->supplier_id;
                            $inventaris->save();
                        }
                    }
                }
            }
            
            // Delete details that are not in the request
            $pengeluaran->detailPengeluaran()->whereNotIn('id', $currentDetailIds)->delete();
            
            // Update total
            $pengeluaran->total_biaya = $totalBiaya;
            $pengeluaran->save();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pengeluaran berhasil diupdate',
                'data' => $pengeluaran->load(['kategori', 'detailPengeluaran.supplier'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate pengeluaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengeluaran = Pengeluaran::find($id);
        if (!$pengeluaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengeluaran tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Hapus bukti pembayaran jika ada
            if ($pengeluaran->bukti_pembayaran) {
                Storage::disk('public')->delete($pengeluaran->bukti_pembayaran);
            }
            
            // Detail pengeluaran akan otomatis terhapus karena cascadeOnDelete
            $pengeluaran->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pengeluaran berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus pengeluaran: ' . $e->getMessage()
            ], 500);
        }
    }
}
