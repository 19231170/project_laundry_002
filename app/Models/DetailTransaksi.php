<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;
    
    protected $table = 'detail_transaksi';
    
    protected $fillable = [
        'transaksi_id',
        'layanan_id',
        'jumlah',
        'harga_satuan',
        'subtotal'
    ];
    
    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];
    
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
    
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}
