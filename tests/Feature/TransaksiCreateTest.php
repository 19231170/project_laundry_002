<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Layanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaksi_create_page_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        Layanan::factory()->create();

        $response = $this->actingAs($user)->get('/transaksi/create');

        $response->assertStatus(200);
        $response->assertSee('Tambah Transaksi');
    }

    public function test_transaksi_create_page_has_pelanggan_modal(): void
    {
        $user = User::factory()->create();
        Layanan::factory()->create();

        $response = $this->actingAs($user)->get('/transaksi/create');

        $response->assertStatus(200);
        $response->assertSee('pelangganModal');
        $response->assertSee('Tambah Pelanggan Baru');
        $response->assertSee('openPelangganModal');
    }

    public function test_transaksi_create_page_has_layanan_modal(): void
    {
        $user = User::factory()->create();
        Layanan::factory()->create();

        $response = $this->actingAs($user)->get('/transaksi/create');

        $response->assertStatus(200);
        $response->assertSee('layananModal');
        $response->assertSee('Tambah Layanan Baru');
        $response->assertSee('openLayananModal');
    }

    public function test_web_api_pelanggan_post_creates_pelanggan(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson('/web-api/pelanggan', [
                'nama' => 'Test Pelanggan',
                'telepon' => '081234567890',
                'email' => 'test@example.com',
                'alamat' => 'Jl. Test No. 1',
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
        ]);
        $this->assertDatabaseHas('pelanggan', [
            'nama' => 'Test Pelanggan',
            'telepon' => '081234567890',
        ]);
    }

    public function test_web_api_layanan_post_creates_layanan(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson('/web-api/layanan', [
                'nama_layanan' => 'Test Layanan',
                'satuan' => 'KG',
                'harga' => 15000,
                'estimasi_waktu' => 2,
                'deskripsi' => 'Test deskripsi',
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
        ]);
        $this->assertDatabaseHas('layanan', [
            'nama_layanan' => 'Test Layanan',
            'satuan' => 'KG',
            'harga' => 15000,
        ]);
    }
}
