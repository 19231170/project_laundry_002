<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;
    
    protected $table = 'layanan';
    
    protected $fillable = [
        'nama_layanan',
        'satuan',
        'harga',
        'deskripsi',
        'estimasi_waktu'
    ];
    
    protected $casts = [
        'harga' => 'decimal:2'
    ];
    
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}
