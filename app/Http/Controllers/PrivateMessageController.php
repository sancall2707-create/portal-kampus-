<?php

namespace App\Http\Controllers;

use App\Models\PrivateMessage;
use App\Models\Unit;
use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PrivateMessageController extends Controller
{
    /**
     * Tampilkan kotak masuk dan pesan terkirim.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'inbox'); // inbox or sent

        // Kotak Masuk: Pesan khusus untuk user saat ini
        $inboxMessages = PrivateMessage::with('sender')
            ->where('recipient_id', $user->id)
            ->whereNull('deleted_by_recipient_at')
            ->orderByDesc('created_at')
            ->get();

        $unreadCount = PrivateMessage::where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->whereNull('deleted_by_recipient_at')
            ->count();

        // Pesan Terkirim: Dikelompokkan per batch_code agar rapi jika kirim ke banyak orang
        $sentRaw = PrivateMessage::with('recipient')
            ->where('sender_id', $user->id)
            ->whereNull('deleted_by_sender_at')
            ->orderByDesc('created_at')
            ->get();

        $sentBatches = [];
        $grouped = $sentRaw->groupBy(fn ($item) => $item->batch_code ?? ('single_' . $item->id));

        foreach ($grouped as $batchKey => $items) {
            $first = $items->first();
            $total = $items->count();
            $readCount = $items->whereNotNull('read_at')->count();

            $sentBatches[] = (object) [
                'id'              => $first->id,
                'batch_code'      => $first->batch_code,
                'subject'         => $first->subject,
                'body'            => $first->body,
                'category'        => $first->category,
                'created_at'      => $first->created_at,
                'total_recipients'=> $total,
                'read_count'      => $readCount,
                'recipients'      => $items->pluck('recipient')->filter(),
                'sample_item'     => $first,
            ];
        }

        return view('messages.index', compact('inboxMessages', 'sentBatches', 'tab', 'unreadCount', 'user'));
    }

    /**
     * Form penulisan pesan satu arah pribadi (Dukungan s.d 50 penerima).
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

        $units = Unit::where('is_active', true)->orderBy('sort_order')->get();

        return view('messages.create', compact('recipients', 'units', 'user'));
    }

    /**
     * Kirim pesan pribadi satu arah (Maksimal 50 penerima sekaligus).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'recipient_ids'   => ['required', 'array', 'min:1', 'max:50'],
            'recipient_ids.*' => ['required', 'exists:users,id', 'different:' . $user->id],
            'category'        => ['required', 'in:arahan,instruksi,teguran,apresiasi,rahasia,umum'],
            'subject'         => ['required', 'string', 'max:200'],
            'body'            => ['required', 'string', 'max:5000'],
        ], [
            'recipient_ids.required' => 'Pilih minimal 1 orang pegawai penerima pesan.',
            'recipient_ids.min'      => 'Pilih minimal 1 orang pegawai penerima pesan.',
            'recipient_ids.max'      => 'Maksimal pengiriman pesan adalah 50 penerima sekaligus.',
            'subject.required'       => 'Perihal atau judul pesan wajib diisi.',
            'body.required'          => 'Isi pesan tidak boleh kosong.',
        ]);

        $recipientIds = array_values(array_unique($validated['recipient_ids']));
        if (count($recipientIds) > 50) {
            return back()->withInput()->withErrors([
                'recipient_ids' => 'Batas kuota terlampaui: Maksimal 50 penerima sekaligus dalam satu kali kirim.'
            ]);
        }

        $batchCode = 'BMSG' . date('ymd') . strtoupper(Str::random(6));
        $now = now();
        $createdMessages = [];

        DB::transaction(function () use ($user, $recipientIds, $batchCode, $validated, $now, &$createdMessages) {
            foreach ($recipientIds as $recId) {
                $msg = PrivateMessage::create([
                    'sender_id'       => $user->id,
                    'recipient_id'    => $recId,
                    'batch_code'      => $batchCode,
                    'category'        => $validated['category'],
                    'subject'         => $validated['subject'],
                    'body'            => $validated['body'],
                    'is_confidential' => true,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
                $createdMessages[] = $msg;
            }
        });

        // Trigger Web Push Notification ke Setiap Smartphone Penerima
        try {
            $pushService = app(WebPushService::class);
            if ($pushService->isConfigured()) {
                $categoryLabel = ucfirst($validated['category']);
                $targetUsers = User::whereIn('id', $recipientIds)->get();

                foreach ($targetUsers as $recUser) {
                    $recMsg = collect($createdMessages)->firstWhere('recipient_id', $recUser->id);
                    $pushService->sendToUser($recUser, [
                        'title' => "🔒 Pesan ({$categoryLabel})",
                        'body'  => "Dari {$user->name}: {$validated['subject']}",
                        'url'   => route('messages.show', $recMsg ? $recMsg->id : ''),
                    ]);
                }
            }
        } catch (\Throwable $e) {}

        $count = count($recipientIds);
        return redirect()->route('messages.index', ['tab' => 'sent'])
            ->with('success', "Pesan satu arah berhasil dikirimkan secara rahasia ke {$count} pegawai.");
    }

    /**
     * Buka dan baca pesan pribadi.
     */
    public function show($id)
    {
        $user = Auth::user();
        $message = PrivateMessage::with(['sender', 'recipient'])->findOrFail($id);

        // Keamanan Ketat: Hanya pengirim atau penerima yang bersangkutan
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403, 'Akses Ditolak: Pesan ini bersifat pribadi dan rahasia antara pengirim dan penerima.');
        }

        // Jika pembuka adalah penerima -> catat waktu baca
        if ($message->recipient_id === $user->id && $message->read_at === null) {
            $message->update(['read_at' => now()]);
        }

        // Jika pembuka adalah pengirim dan pesan ini bagian dari batch kirim massal:
        $batchRecipients = null;
        if ($message->sender_id === $user->id && $message->batch_code) {
            $batchRecipients = PrivateMessage::with('recipient')
                ->where('sender_id', $user->id)
                ->where('batch_code', $message->batch_code)
                ->orderBy('recipient_id')
                ->get();
        }

        return view('messages.show', compact('message', 'batchRecipients', 'user'));
    }

    /**
     * Hapus pesan dari kotak riwayat.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $message = PrivateMessage::findOrFail($id);

        if ($message->recipient_id === $user->id) {
            $message->update(['deleted_by_recipient_at' => now()]);
        } elseif ($message->sender_id === $user->id) {
            // Jika sender menghapus batch
            if ($message->batch_code) {
                PrivateMessage::where('sender_id', $user->id)
                    ->where('batch_code', $message->batch_code)
                    ->update(['deleted_by_sender_at' => now()]);
            } else {
                $message->update(['deleted_by_sender_at' => now()]);
            }
        } else {
            abort(403);
        }

        return back()->with('success', 'Pesan berhasil dihapus dari daftar Anda.');
    }
}
