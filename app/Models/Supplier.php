<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    
    protected $table = 'supplier';
    
    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'email',
        'website',
        'pic_nama',
        'pic_kontak',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    public function detailPengeluaran()
    {
        return $this->hasMany(DetailPengeluaran::class);
    }
    
    public function inventaris()
    {
        return $this->hasMany(Inventaris::class);
    }
}
