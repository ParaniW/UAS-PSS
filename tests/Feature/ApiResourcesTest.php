<?php

namespace Tests\Feature;

use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_requires_auth_and_returns_user()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me')
            ->assertStatus(200)
            ->assertJsonFragment(['email' => $user->email]);
    }

    public function test_protected_routes_require_auth()
    {
        $this->getJson('/api/me')->assertStatus(401);
        $this->postJson('/api/dokter', [])->assertStatus(401);
    }

    public function test_create_poli_and_list()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test-token')->plainTextToken;

        $payload = [
            'nama_poli' => 'Poli Test',
            'keterangan' => 'Keterangan',
            'tarif' => 75000,
        ];

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/poli', $payload)
            ->assertStatus(201)
            ->assertJsonFragment(['nama_poli' => 'Poli Test']);

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/poli')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_create_dokter_validation_and_success()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        // missing required fields -> validation error
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/dokter', [])
            ->assertStatus(422);

        // create poli then dokter
        $poli = Poli::factory()->create();

        $payload = [
            'nama' => 'Dr. Test',
            'email' => 'drtest@example.com',
            'password' => 'dokter123',
            'id_poli' => $poli->id,
        ];

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/dokter', $payload)
            ->assertStatus(201)
            ->assertJsonFragment(['nama' => 'Dr. Test']);
    }

    public function test_create_obat_and_pasien()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $obat = [
            'nama_obat' => 'Paracetamol',
            'kemasan' => 'Strip',
            'harga' => 5000,
            'stok' => 100,
        ];

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/obat', $obat)
            ->assertStatus(201)
            ->assertJsonFragment(['nama_obat' => 'Paracetamol']);

        $pasien = [
            'nama' => 'Pasien Baru',
            'email' => 'pasienbaru@example.com',
            'password' => 'password123',
        ];

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/pasien', $pasien)
            ->assertStatus(201)
            ->assertJsonFragment(['email' => 'pasienbaru@example.com']);
    }
}
