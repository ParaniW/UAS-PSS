<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_returns_token_and_user()
    {
        $response = $this->postJson('/api/register', [
            'nama' => 'Test Pasien',
            'email' => 'test@example.com',
            'password' => 'password',
            'alamat' => 'Jalan Test',
            'no_ktp' => '1234567890123456',
            'no_hp' => '081234567890',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'token', 'token_type', 'user']);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'pasien']);
    }

    public function test_login_returns_token()
    {
        $user = User::factory()->create(['email' => 'login@example.com', 'password' => 'password']);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'token_type', 'user']);
    }
}
