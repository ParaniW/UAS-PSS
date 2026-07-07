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

        // 2. Definisi Throttling Berdasarkan 3 Role Keamanan
        RateLimiter::for('role-limits', function (Request $request) {
            $user = $request->user();

            // Jika user belum login, batasi berdasarkan IP pengunjung (misal: 20 request per menit)
            if (!$user) {
                return Limit::perMinute(20)->by($request->ip());
            }

            // Bedakan batasan (Limit) berdasarkan string Role yang ada di database kamu
            return match ($user->role) {
                'admin'  => Limit::perMinute(100)->by($user->id),  // Admin: 100 request/menit
                'dokter' => Limit::perMinute(60)->by($user->id),   // Dokter: 60 request/menit
                'pasien' => Limit::perMinute(30)->by($user->id),   // Pasien: 30 request/menit
                default  => Limit::perMinute(30)->by($user->id),
            };
        });
    }
}
