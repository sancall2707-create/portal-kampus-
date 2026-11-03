<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GreetingController extends Controller
{
    /**
     * Tampilkan layar sapaan wajib Koordinator.
     */
    public function show()
    {
        $user = Auth::user();

        // Ambil sapaan aktif saat ini
        $now = now();
        $greeting = DB::table('greetings')
            ->where('is_active', true)
            ->where('display_start', '<=', $now)
            ->where('display_end', '>=', $now)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->first();

        // Jika tidak ada sapaan aktif, langsung ke dashboard
        if (!$greeting) {
            return redirect()->route('dashboard');
        }

        // Ambil highlight informasi penting terkait sapaan ini
        $highlights = DB::table('greeting_highlights')
            ->where('greeting_id', $greeting->id)
            ->orderBy('sort_order')
            ->get();

        // Ambil pembuat sapaan (Koordinator)
        $author = DB::table('users')->where('id', $greeting->created_by)->first();

        // Periksa apakah user sudah pernah membaca sapaan ini
        $alreadyRead = DB::table('greeting_reads')
            ->where('greeting_id', $greeting->id)
            ->where('user_id', $user->id)
            ->exists();

        return view('greetings.show', compact('greeting', 'highlights', 'author', 'alreadyRead', 'user'));
    }

    /**
     * Catat status baca dan teruskan pengguna ke Beranda.
     */
    public function acknowledge(Request $request)
    {
        $user = Auth::user();
        $greetingId = $request->input('greeting_id');

        if ($greetingId) {
            DB::table('greeting_reads')->updateOrInsert(
                [
                    'greeting_id' => $greetingId,
                    'user_id'     => $user->id,
                ],
                [
                    'read_at' => now(),
                ]
            );
        }

        // Simpan penanda di session bahwa sesi ini sudah melihat sapaan hari ini
        $request->session()->put('greeting_acknowledged_' . $greetingId, true);

        return redirect()->route('dashboard');
    }
}
