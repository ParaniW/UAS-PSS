<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Memaksa Laravel menggunakan HTTPS di server cloud (Railway)
        if (config('app.env') === 'production' || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // 2. Definisi Throttling Berdasarkan 3 Role Keamanan (Diperlonggar)
        RateLimiter::for('role-limits', function (Request $request) {
            $user = $request->user();

            // Jika user belum login, berikan batas aman yang lebih besar saat pertama load
            if (!$user) {
                return Limit::perMinute(200)->by($request->ip());
            }

            // Longgarkan limit per menit agar aman dari false-positive di server cloud
            return match ($user->role) {
                'admin'  => Limit::perMinute(500)->by($user->id),  // Admin: 500 request/menit
                'dokter' => Limit::perMinute(300)->by($user->id),  // Dokter: 300 request/menit
                'pasien' => Limit::perMinute(300)->by($user->id),  // Pasien: 300 request/menit
                default  => Limit::perMinute(300)->by($user->id),
            };
        });
    }
}
