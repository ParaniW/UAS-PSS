<?php

namespace Tests\Feature;

use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiDokterTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_dokter_returns_paginated_results()
    {
        $poli = Poli::factory()->create();
        User::factory()->count(20)->create(['role' => 'dokter', 'id_poli' => $poli->id]);

        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/dokter?per_page=5');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }
}
