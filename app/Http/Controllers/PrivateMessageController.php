<?php

namespace App\Http\Controllers;

use App\Models\PrivateMessage;
use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivateMessageController extends Controller
{
    /**
     * Tampilkan kotak masuk dan pesan terkirim (satu arah pribadi).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'inbox'); // inbox or sent

        $inboxQuery = PrivateMessage::with('sender')
            ->where('recipient_id', $user->id)
            ->whereNull('deleted_by_recipient_at')
            ->orderByDesc('created_at');

        $sentQuery = PrivateMessage::with('recipient')
            ->where('sender_id', $user->id)
            ->whereNull('deleted_by_sender_at')
            ->orderByDesc('created_at');

        $unreadCount = PrivateMessage::where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->whereNull('deleted_by_recipient_at')
            ->count();

        $messages = ($tab === 'sent') ? $sentQuery->get() : $inboxQuery->get();

        return view('messages.index', compact('messages', 'tab', 'unreadCount', 'user'));
    }

    /**
     * Form penulisan pesan satu arah pribadi.
     */
    public function create()
    {
        $user = Auth::user();

        // Daftar seluruh pegawai aktif (kecuali diri sendiri)
        $recipients = User::where('id', '!=', $user->id)
            ->where('status', 'active')
            ->with('units')
            ->orderBy('name')
            ->get();

        return view('messages.create', compact('recipients', 'user'));
    }

    /**
     * Kirim pesan pribadi satu arah & kirim notifikasi push ke HP penerima.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'recipient_id' => ['required', 'exists:users,id', 'different:' . $user->id],
            'category'     => ['required', 'in:arahan,instruksi,teguran,apresiasi,rahasia,umum'],
            'subject'      => ['required', 'string', 'max:200'],
            'body'         => ['required', 'string', 'max:5000'],
        ], [
            'recipient_id.required' => 'Pilih pegawai tujuan penerima pesan.',
            'subject.required'      => 'Perihal atau judul pesan wajib diisi.',
            'body.required'         => 'Isi pesan tidak boleh kosong.',
        ]);

        $message = PrivateMessage::create([
            'sender_id'       => $user->id,
            'recipient_id'    => $validated['recipient_id'],
            'category'        => $validated['category'],
            'subject'         => $validated['subject'],
            'body'            => $validated['body'],
            'is_confidential' => true,
        ]);

        // Kirim Web Push Notification ke Smartphone Penerima
        try {
            $pushService = app(WebPushService::class);
            $recipient = User::find($validated['recipient_id']);
            if ($pushService->isConfigured() && $recipient) {
                $categoryLabel = ucfirst($validated['category']);
                $pushService->sendToUser($recipient, [
                    'title' => "🔒 Pesan Pribadi ({$categoryLabel})",
                    'body'  => "Dari {$user->name}: {$validated['subject']}",
                    'url'   => route('messages.show', $message->id),
                ]);
            }
        } catch (\Throwable $e) {}

        return redirect()->route('messages.index', ['tab' => 'sent'])
            ->with('success', 'Pesan satu arah pribadi berhasil dikirim secara rahasia ke penerima.');
    }

    /**
     * Buka dan baca pesan pribadi (Otorisasi ketat: HANYA Pengirim & Penerima).
     */
    public function show($id)
    {
        $user = Auth::user();
        $message = PrivateMessage::with(['sender', 'recipient'])->findOrFail($id);

        // Keamanan Ketat: Jangan izinkan orang lain selain pengirim atau penerima
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403, 'Akses Ditolak: Pesan ini bersifat pribadi dan rahasia antara pengirim dan penerima.');
        }

        // Jika pembuka adalah penerima dan belum dibaca -> catat read_at
        if ($message->recipient_id === $user->id && $message->read_at === null) {
            $message->update(['read_at' => now()]);
        }

        return view('messages.show', compact('message', 'user'));
    }

    /**
     * Hapus pesan dari kotak masuk / kotak keluar pemohon.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $message = PrivateMessage::findOrFail($id);

        if ($message->recipient_id === $user->id) {
            $message->update(['deleted_by_recipient_at' => now()]);
        } elseif ($message->sender_id === $user->id) {
            $message->update(['deleted_by_sender_at' => now()]);
        } else {
            abort(403);
        }

        return back()->with('success', 'Pesan berhasil dihapus dari daftar Anda.');
    }
}
