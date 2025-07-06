<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengeluaran extends Model
{
    use HasFactory;
    
    protected $table = 'detail_pengeluaran';
    
    protected $fillable = [
        'pengeluaran_id',
        'nama_item',
        'jumlah',
        'satuan',
        'harga_satuan',
        'subtotal',
        'supplier_id',
    ];
    
    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];
    
    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class);
    }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
