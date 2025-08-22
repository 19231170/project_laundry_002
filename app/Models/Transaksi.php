<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    
    protected $table = 'transaksi';
    
    protected $fillable = [
        'kode_transaksi',
        'pelanggan_id',
        'total_harga',
        'pembulatan',
        'total_setelah_pembulatan',
        'tanggal_masuk',
        'tanggal_selesai',
        'status',
        'status_pembayaran',
        'tanggal_pembayaran',
        'jumlah_dibayar',
        'sisa_pembayaran',
        'catatan'
    ];
    
    protected $casts = [
        'total_harga' => 'decimal:2',
        'pembulatan' => 'integer',
        'total_setelah_pembulatan' => 'decimal:2',
        'tanggal_masuk' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_pembayaran' => 'date',
        'jumlah_dibayar' => 'decimal:2',
        'sisa_pembayaran' => 'decimal:2'
    ];
    
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
    
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
    
    public static function generateKodeTransaksi()
    {
        $lastTransaction = self::latest('id')->first();
        $lastNumber = $lastTransaction ? intval(substr($lastTransaction->kode_transaksi, -4)) : 0;
        $newNumber = $lastNumber + 1;
        
        return 'TRX' . date('Ymd') . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
