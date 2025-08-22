<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Layanan;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layanan = [
            [
                'nama_layanan' => 'Cuci Kering',
                'satuan' => 'KG',
                'harga' => 5000,
                'deskripsi' => 'Layanan cuci dan pengeringan pakaian',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_layanan' => 'Cuci Setrika',
                'satuan' => 'KG',
                'harga' => 7000,
                'deskripsi' => 'Layanan cuci, pengeringan, dan setrika pakaian',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_layanan' => 'Cuci Sepatu',
                'satuan' => 'PCS',
                'harga' => 15000,
                'deskripsi' => 'Layanan cuci sepatu dengan perawatan khusus',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_layanan' => 'Cuci Tas',
                'satuan' => 'PCS',
                'harga' => 20000,
                'deskripsi' => 'Layanan cuci tas dengan perawatan khusus',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_layanan' => 'Cuci Boneka',
                'satuan' => 'PCS',
                'harga' => 25000,
                'deskripsi' => 'Layanan cuci boneka dengan perawatan lembut',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_layanan' => 'Cuci Gordyn',
                'satuan' => 'M',
                'harga' => 12000,
                'deskripsi' => 'Layanan cuci gordyn per meter',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_layanan' => 'Dry Clean',
                'satuan' => 'PCS',
                'harga' => 35000,
                'deskripsi' => 'Layanan dry cleaning untuk pakaian premium',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_layanan' => 'Cuci Selimut',
                'satuan' => 'PCS',
                'harga' => 30000,
                'deskripsi' => 'Layanan cuci selimut dan bed cover',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        Layanan::insert($layanan);
    }
}
