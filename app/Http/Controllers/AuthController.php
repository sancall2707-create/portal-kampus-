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
        $userUnitIds = $user->units->pluck('id')->toArray();
        $now = now();

        // Feed pengumuman terbaru sesuai cakupan unit & hak akses pengguna
        $announcementQuery = DB::table('announcements')
            ->join('users', 'users.id', '=', 'announcements.sender_id')
            ->leftJoin('units', 'units.id', '=', 'announcements.unit_id')
            ->select(
                'announcements.id',
                'announcements.body',
                'announcements.scope',
                'announcements.category',
                'announcements.is_urgent',
                'announcements.publish_at',
                'users.name as sender_name',
                'units.code as unit_code'
            )
            ->where('announcements.status', 'published')
            ->where('announcements.publish_at', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('announcements.display_until')
                  ->orWhere('announcements.display_until', '>=', $now);
            })
            ->whereNull('announcements.deleted_at');

        if (! $user->hasRole('koordinator')) {
            $announcementQuery->where(function ($q) use ($userUnitIds) {
                $q->where('announcements.scope', 'campus')
                  ->orWhereIn('announcements.unit_id', $userUnitIds);
            });
        }

        $latestAnnouncements = $announcementQuery
            ->orderByDesc('announcements.is_urgent')
            ->orderByDesc('announcements.publish_at')
            ->limit(4)
            ->get();

        $readAnnouncementIds = DB::table('announcement_recipients')
            ->where('user_id', $user->id)
            ->whereNotNull('read_at')
            ->pluck('announcement_id')
            ->toArray();

        // Feed agenda terdekat
        $agendaQuery = DB::table('agendas')
            ->leftJoin('units', 'units.id', '=', 'agendas.unit_id')
            ->leftJoin('facilities', 'facilities.id', '=', 'agendas.facility_id')
            ->select(
                'agendas.id',
                'agendas.title',
                'agendas.category',
                'agendas.start_at',
                'agendas.end_at',
                'agendas.location_text',
                'units.code as unit_code',
                'facilities.name as facility_name'
            )
            ->whereNull('agendas.deleted_at')
            ->where('agendas.end_at', '>=', $now);

        if (! $user->hasRole('koordinator')) {
            $agendaQuery->where(function ($q) use ($userUnitIds, $user) {
                $q->where('agendas.category', 'campus')
                  ->orWhereIn('agendas.unit_id', $userUnitIds)
                  ->orWhere('agendas.created_by', $user->id);
            });
        }

        $upcomingAgendas = $agendaQuery->orderBy('agendas.start_at')->limit(4)->get();

        // Ringkasan angka untuk kartu statistik beranda
        $stats = [
            'unread_announcements' => DB::table('announcement_recipients')
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count(),
            'unread_messages' => DB::table('private_messages')
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->whereNull('deleted_by_recipient_at')
                ->count(),
            'pending_leaves' => DB::table('leave_requests')
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending_unit', 'pending_coordinator'])
                ->whereNull('deleted_at')
                ->count(),
            'active_bookings' => DB::table('facility_bookings')
                ->where('requester_id', $user->id)
                ->whereIn('status', ['pending_screening', 'pending_unit', 'pending_coordinator', 'approved'])
                ->whereNull('deleted_at')
                ->count(),
        ];

        return view('dashboard.index', compact(
            'user',
            'primaryUnit',
            'latestAnnouncements',
            'readAnnouncementIds',
            'upcomingAgendas',
            'stats'
        ));
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
