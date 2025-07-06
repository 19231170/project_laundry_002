<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    use HasFactory;
    
    protected $table = 'inventaris';
    
    protected $fillable = [
        'nama_barang',
        'kategori_id',
        'jumlah_stok',
        'satuan',
        'harga_beli_terakhir',
        'minimal_stok',
        'supplier_id',
        'tanggal_beli_terakhir',
        'lokasi_penyimpanan',
    ];
    
    protected $casts = [
        'jumlah_stok' => 'decimal:2',
        'harga_beli_terakhir' => 'decimal:2',
        'minimal_stok' => 'decimal:2',
        'tanggal_beli_terakhir' => 'date',
    ];
    
    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_id');
    }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    // Check if item is low on stock
    public function isLowStock()
    {
        return $this->jumlah_stok <= $this->minimal_stok;
    }
}
