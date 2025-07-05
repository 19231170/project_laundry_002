<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pelanggan = [
            [
                'nama' => 'Budi Santoso',
                'telepon' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta',
                'email' => 'budi@email.com',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'telepon' => '081234567891',
                'alamat' => 'Jl. Sudirman No. 456, Jakarta',
                'email' => 'siti@email.com',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama' => 'Ahmad Wijaya',
                'telepon' => '081234567892',
                'alamat' => 'Jl. Thamrin No. 789, Jakarta',
                'email' => 'ahmad@email.com',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama' => 'Dewi Lestari',
                'telepon' => '081234567893',
                'alamat' => 'Jl. Gatot Subroto No. 101, Jakarta',
                'email' => 'dewi@email.com',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama' => 'Rudi Hermawan',
                'telepon' => '081234567894',
                'alamat' => 'Jl. Kuningan No. 202, Jakarta',
                'email' => 'rudi@email.com',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        Pelanggan::insert($pelanggan);
    }
}
