<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaksi>
 */
class TransaksiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_transaksi' => 'TRX'.now()->format('Ymd').fake()->unique()->numberBetween(1000, 9999),
            'pelanggan_id' => Pelanggan::factory(),
            'total_harga' => fake()->numberBetween(10000, 250000),
            'pembulatan' => 0,
            'total_setelah_pembulatan' => fn (array $attributes): int => $attributes['total_harga'],
            'tanggal_masuk' => now()->toDateString(),
            'tanggal_selesai' => null,
            'status' => fake()->randomElement(['pending', 'proses', 'selesai', 'diambil']),
            'status_pembayaran' => 'belum_lunas',
            'tanggal_pembayaran' => null,
            'jumlah_dibayar' => 0,
            'sisa_pembayaran' => fn (array $attributes): int => $attributes['total_setelah_pembulatan'],
            'catatan' => null,
        ];
    }
}
