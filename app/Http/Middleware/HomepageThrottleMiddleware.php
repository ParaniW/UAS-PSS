<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HomepageThrottleMiddleware
{
    public function handle(Request $request, Closure $next, $maxHits = 5): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        $maxHits = (int) $maxHits;
        $sessionKey = sprintf('homepage_hit_count_%s', $user->id);
        $count = $request->session()->get($sessionKey, 0);
        $count++;

        if ($count > $maxHits) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Batas akses homepage telah tercapai. Silakan login ulang.');
        }

        $request->session()->put($sessionKey, $count);

        return $next($request);
    }
}
