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

        // 2. Definisi Throttling Berdasarkan 3 Role Keamanan (Mode Aman Cloud)
        RateLimiter::for('role-limits', function (Request $request) {
            $user = $request->user();

            // JIKA USER BELUM LOGIN: Berikan batas sangat longgar agar aset web tidak memicu 429
            if (!$user) {
                return Limit::none(); // Menghilangkan batasan untuk tamu agar halaman login aman terbuka
            }

            // JIKA SUDAH LOGIN: Batasi ketat berdasarkan role masing-masing demi keamanan
            return match ($user->role) {
                'admin'  => Limit::perMinute(500)->by($user->id),
                'dokter' => Limit::perMinute(300)->by($user->id),
                'pasien' => Limit::perMinute(300)->by($user->id),
                default  => Limit::perMinute(300)->by($user->id),
            };
        });
    }
}
