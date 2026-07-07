<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageThrottleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_throttle_logs_out_after_five_visits_for_each_role()
    {
        $roles = [
            'admin' => '/admin/dashboard',
            'dokter' => '/dokter/dashboard',
            'pasien' => '/pasien/dashboard',
        ];

        foreach ($roles as $role => $path) {
            $user = User::factory()->create(['role' => $role]);

            for ($i = 1; $i <= 5; $i++) {
                $response = $this->actingAs($user)->get($path);
                $response->assertStatus(200);
            }

            $response = $this->actingAs($user)->get($path);
            $response->assertRedirect(route('login'));
            $response->assertSessionHas('error', 'Batas akses homepage telah tercapai. Silakan login ulang.');
        }
    }
}
