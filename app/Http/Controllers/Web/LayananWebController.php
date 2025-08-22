<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LayananWebController extends Controller
{
    public function index()
    {
        $layanan = Layanan::latest()->paginate(10);
        return view('layanan.index', compact('layanan'));
    }

    public function create()
    {
        return view('layanan.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_layanan' => 'required|string|max:255',
            'satuan' => 'required|in:KG,PCS,M',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Layanan::create($request->all());
        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil ditambahkan');
    }

    public function show(Layanan $layanan)
    {
        $layanan->load(['detailTransaksi.transaksi.pelanggan']);
        return view('layanan.show', compact('layanan'));
    }

    public function edit(Layanan $layanan)
    {
        return view('layanan.edit', compact('layanan'));
    }

    public function update(Request $request, Layanan $layanan)
    {
        $validator = Validator::make($request->all(), [
            'nama_layanan' => 'required|string|max:255',
            'satuan' => 'required|in:KG,PCS,M',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $layanan->update($request->all());
        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil diupdate');
    }

    public function destroy(Layanan $layanan)
    {
        if ($layanan->detailTransaksi()->count() > 0) {
            return redirect()->route('layanan.index')->with('error', 'Layanan tidak dapat dihapus karena masih digunakan dalam transaksi');
        }

        $layanan->delete();
        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil dihapus');
    }
}
