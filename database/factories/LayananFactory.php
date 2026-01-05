<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Layanan>
 */
class LayananFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_layanan' => fake()->randomElement(['Cuci Kering', 'Cuci Setrika', 'Setrika Saja', 'Express']),
            'satuan' => fake()->randomElement(['KG', 'PCS']),
            'harga' => fake()->numberBetween(5000, 20000),
            'deskripsi' => fake()->sentence(),
            'estimasi_waktu' => fake()->numberBetween(1, 5),
        ];
    }
}
