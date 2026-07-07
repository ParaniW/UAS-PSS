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

        // 2. Definisi Throttling Ketat: Maksimal 10 Request per Menit untuk Uji Coba UAS
        RateLimiter::for('role-limits', function (Request $request) {
            $user = $request->user();

            // Jika user belum login, berikan batas longgar agar halaman login & aset tidak macet
            if (!$user) {
                return Limit::none();
            }

            // Ketika sudah login, semua role dikunci maksimal 10 request per menit
            return match ($user->role) {
                'admin'  => Limit::perMinute(10)->by($user->id),
                'dokter' => Limit::perMinute(10)->by($user->id),
                'pasien' => Limit::perMinute(10)->by($user->id),
                default  => Limit::perMinute(10)->by($user->id),
            };
        });
    }
}
