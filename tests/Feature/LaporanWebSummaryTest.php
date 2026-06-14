<?php

namespace Tests\Feature;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanWebSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_pendapatan_uses_rounded_total(): void
    {
        $user = User::factory()->create();

        Transaksi::factory()->create([
            'total_harga' => 10000,
            'total_setelah_pembulatan' => 10100,
            'tanggal_masuk' => '2026-05-10',
        ]);

        Transaksi::factory()->create([
            'total_harga' => 20000,
            'total_setelah_pembulatan' => 20000,
            'tanggal_masuk' => '2026-05-20',
        ]);

        Transaksi::factory()->create([
            'total_harga' => 50000,
            'total_setelah_pembulatan' => 99999,
            'tanggal_masuk' => '2026-04-15',
        ]);

        $response = $this->actingAs($user)->get(route('laporan.index', [
            'tanggal_mulai' => '2026-05-01',
            'tanggal_selesai' => '2026-05-31',
        ]));

        $response->assertOk();
        $response->assertViewHas('summary', function (array $summary): bool {
            return $summary['total_pendapatan'] === 30100;
        });
    }
}
