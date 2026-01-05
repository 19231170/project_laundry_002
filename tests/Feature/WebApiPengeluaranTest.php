<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebApiPengeluaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_api_requires_authentication(): void
    {
        $response = $this->getJson('/web-api/pengeluaran');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_kategori_pengeluaran(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/web-api/kategori-pengeluaran');

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'data']);
    }

    public function test_authenticated_user_can_access_supplier(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/web-api/supplier');

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'data']);
    }

    public function test_authenticated_user_can_access_pengeluaran(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/web-api/pengeluaran');

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'data']);
    }

    public function test_authenticated_user_can_access_laporan_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/web-api/laporan/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'data']);
    }
}
