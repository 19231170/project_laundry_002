<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;
    
    protected $table = 'pengeluaran';
    
    protected $fillable = [
        'tanggal',
        'total_biaya',
        'kategori_pengeluaran_id',
        'keterangan',
        'bukti_pembayaran',
        'user_id',
        'supplier_id',
        'penerima',
    ];
    
    protected $casts = [
        'tanggal' => 'date',
        'total_biaya' => 'decimal:2',
    ];
    
    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_pengeluaran_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function detailPengeluaran()
    {
        return $this->hasMany(DetailPengeluaran::class);
    }
}
