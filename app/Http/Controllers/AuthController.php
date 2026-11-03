<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login utama.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login staf (NIP / Email).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim($credentials['login']);
        $password   = $credentials['password'];
        $remember   = $request->boolean('remember');

        // Cari berdasarkan NIP (huruf besar) atau Email
        $user = User::where('nip', strtoupper($loginInput))
            ->orWhere('email', strtolower($loginInput))
            ->first();

        $ip = $request->ip();
        $ua = substr($request->userAgent() ?? '', 0, 255);

        if (!$user || !password_verify($password, $user->password)) {
            // Catat kegagalan login ke login_logs
            DB::table('login_logs')->insert([
                'user_id'      => $user?->id,
                'identifier'   => $loginInput,
                'method'       => 'nip',
                'success'      => false,
                'ip_address'   => $ip,
                'user_agent'   => $ua,
                'logged_in_at' => now(),
            ]);

            return back()->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => 'NIP atau kata sandi tidak cocok.']);
        }

        if ($user->status !== 'active') {
            return back()->withInput($request->only('login'))
                ->withErrors(['login' => 'Akun Anda sedang dinonaktifkan atau ditangguhkan. Hubungi Koordinator Kampus.']);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Update login_logs & last_login_at
        $now = now();
        $user->update(['last_login_at' => $now]);

        DB::table('login_logs')->insert([
            'user_id'      => $user->id,
            'identifier'   => $user->nip ?? $user->email,
            'method'       => 'nip',
            'success'      => true,
            'ip_address'   => $ip,
            'user_agent'   => $ua,
            'logged_in_at' => $now,
        ]);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Dashboard sementara sesuai peran dan unit.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $user->load('units');
        $primaryUnit = $user->units->first();

        return view('dashboard.index', compact('user', 'primaryUnit'));
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
