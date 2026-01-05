<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_displays_statistics(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalPelanggan');
        $response->assertViewHas('transaksiHariIni');
        $response->assertViewHas('pendapatanHariIni');
        $response->assertViewHas('transaksiProses');
        $response->assertViewHas('totalBelumLunas');
        $response->assertViewHas('lunasCount');
        $response->assertViewHas('belumLunasCount');
        $response->assertViewHas('persentaseLunas');
        $response->assertViewHas('transaksiTerbaru');
    }

    public function test_dashboard_shows_correct_customer_count(): void
    {
        $user = User::factory()->create();

        // Create customers manually
        for ($i = 0; $i < 5; $i++) {
            Pelanggan::create([
                'nama' => 'Pelanggan '.($i + 1),
                'no_telepon' => '0812345678'.$i,
                'alamat' => 'Alamat '.($i + 1),
            ]);
        }

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalPelanggan', 5);
    }
}
