<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckMandatoryGreeting
{
    /**
     * Intersepsi staf agar melihat layar sapaan wajib Koordinator sebelum masuk dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Hanya berlaku untuk staf internal yang sudah login
        if ($user && $user->user_type === 'staff') {
            $now = now();
            $activeGreeting = DB::table('greetings')
                ->where('is_active', true)
                ->where('display_start', '<=', $now)
                ->where('display_end', '>=', $now)
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->first();

            if ($activeGreeting) {
                $sessionKey = 'greeting_acknowledged_' . $activeGreeting->id;

                if (!$request->session()->get($sessionKey)) {
                    $hasRead = DB::table('greeting_reads')
                        ->where('greeting_id', $activeGreeting->id)
                        ->where('user_id', $user->id)
                        ->exists();

                    if (!$hasRead) {
                        return redirect()->route('greeting.show');
                    }

                    $request->session()->put($sessionKey, true);
                }
            }
        }

        return $next($request);
    }
}
