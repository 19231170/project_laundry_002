<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkTransaksiActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_status_transaksi_updates_selected_transaksi(): void
    {
        $user = User::factory()->create();
        $transaksiA = Transaksi::factory()->create(['status' => 'pending']);
        $transaksiB = Transaksi::factory()->create(['status' => 'pending']);
        $transaksiC = Transaksi::factory()->create(['status' => 'proses']);

        $response = $this->actingAs($user)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->put(route('transaksi.bulk-action'), [
                'action' => 'status_transaksi',
                'status_transaksi' => 'selesai',
                'transaksi_ids' => [$transaksiA->id, $transaksiB->id],
            ]);

        $response->assertRedirect(route('transaksi.index'));
        $this->assertDatabaseHas('transaksi', ['id' => $transaksiA->id, 'status' => 'selesai']);
        $this->assertDatabaseHas('transaksi', ['id' => $transaksiB->id, 'status' => 'selesai']);
        $this->assertDatabaseHas('transaksi', ['id' => $transaksiC->id, 'status' => 'proses']);
    }

    public function test_bulk_status_pembayaran_marks_selected_transaksi_as_lunas(): void
    {
        $user = User::factory()->create();
        $transaksi = Transaksi::factory()->create([
            'status_pembayaran' => 'belum_lunas',
            'total_harga' => 75000,
            'total_setelah_pembulatan' => 75000,
            'jumlah_dibayar' => 0,
            'sisa_pembayaran' => 75000,
        ]);

        $response = $this->actingAs($user)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->put(route('transaksi.bulk-action'), [
                'action' => 'status_pembayaran',
                'status_pembayaran' => 'lunas',
                'transaksi_ids' => [$transaksi->id],
            ]);

        $response->assertRedirect(route('transaksi.index'));

        $transaksi->refresh();
        $this->assertSame('lunas', $transaksi->status_pembayaran);
        $this->assertEquals($transaksi->total_setelah_pembulatan, $transaksi->jumlah_dibayar);
        $this->assertEquals(0, (int) $transaksi->sisa_pembayaran);
        $this->assertNotNull($transaksi->tanggal_pembayaran);
    }
}
