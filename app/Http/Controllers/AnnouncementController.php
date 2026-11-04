<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WebPushService;

class AnnouncementController extends Controller
{
    /**
     * Tampilkan daftar siaran pengumuman aktif & filter kampus/unit.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $filter = $request->get('filter', 'all'); // all, campus, unit
        $now = now();

        $query = DB::table('announcements')
            ->join('users', 'users.id', '=', 'announcements.sender_id')
            ->leftJoin('units', 'units.id', '=', 'announcements.unit_id')
            ->select(
                'announcements.*',
                'users.name as sender_name',
                'users.job_title as sender_title',
                'units.code as unit_code',
                'units.name as unit_name'
            )
            ->where('announcements.status', 'published')
            ->where('announcements.publish_at', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('announcements.display_until')
                  ->orWhere('announcements.display_until', '>=', $now);
            })
            ->whereNull('announcements.deleted_at');

        // Batasi keterlihatan siaran sesuai hak akses & unit user
        if ($user->hasRole('koordinator')) {
            // Koordinator melihat semua siaran kampus & seluruh unit
        } else {
            $query->where(function ($q) use ($userUnitIds) {
                $q->where('announcements.scope', 'campus')
                  ->orWhereIn('announcements.unit_id', $userUnitIds);
            });
        }

        if ($filter === 'campus') {
            $query->where('announcements.scope', 'campus');
        } elseif ($filter === 'unit') {
            $query->where('announcements.scope', 'unit');
        }

        $announcements = $query->orderByDesc('announcements.is_urgent')
            ->orderByDesc('announcements.publish_at')
            ->get();

        // Ambil status baca user saat ini
        $readStatus = DB::table('announcement_recipients')
            ->where('user_id', $user->id)
            ->pluck('read_at', 'announcement_id')
            ->toArray();

        // Cek izin membuat siaran
        $canCreateCampus = $user->can('announcement.create.campus');
        $canCreateUnit   = $user->can('announcement.create.unit');

        return view('announcements.index', compact(
            'announcements', 'readStatus', 'filter', 'canCreateCampus', 'canCreateUnit', 'user'
        ));
    }

    /**
     * Tampilkan form pembuatan broadcast baru.
     */
    public function create()
    {
        $user = Auth::user();
        $user->load('units');

        $canCreateCampus = $user->can('announcement.create.campus');
        $canCreateUnit   = $user->can('announcement.create.unit');

        if (!$canCreateCampus && !$canCreateUnit) {
            abort(403, 'Anda tidak memiliki hak wewenang untuk menyiarkan pengumuman.');
        }

        // Unit yang bisa dipilih sebagai target
        if ($user->hasRole('koordinator')) {
            $availableUnits = Unit::where('is_active', true)->orderBy('sort_order')->get();
        } else {
            $availableUnits = $user->units;
        }

        // Ambil batas karakter dari app_settings
        $maxChars = DB::table('app_settings')->where('setting_key', 'broadcast_max_chars')->value('setting_value') ?? 100;

        return view('announcements.create', compact('availableUnits', 'canCreateCampus', 'canCreateUnit', 'maxChars', 'user'));
    }

    /**
     * Simpan siaran pengumuman baru & fan-out ke penerima.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $maxChars = (int) (DB::table('app_settings')->where('setting_key', 'broadcast_max_chars')->value('setting_value') ?? 100);

        $validated = $request->validate([
            'scope'         => ['required', 'in:campus,unit'],
            'unit_id'       => ['nullable', 'required_if:scope,unit', 'exists:units,id'],
            'category'      => ['required', 'in:new,reminder'],
            'body'          => ['required', 'string', "max:{$maxChars}"],
            'is_urgent'     => ['nullable', 'boolean'],
            'display_days'  => ['nullable', 'integer', 'min:1', 'max:30'],
        ], [
            'body.max' => "Isi pengumuman tidak boleh lebih dari {$maxChars} karakter sesuai aturan sistem.",
        ]);

        // Validasi hak otorisasi scope
        if ($validated['scope'] === 'campus' && !$user->can('announcement.create.campus')) {
            abort(403, 'Anda tidak memiliki izin membuat siaran tingkat kampus.');
        }

        if ($validated['scope'] === 'unit') {
            if (!$user->can('announcement.create.unit')) {
                abort(403, 'Anda tidak memiliki izin membuat siaran tingkat unit.');
            }
            if (!$user->hasRole('koordinator') && !in_array($validated['unit_id'], $userUnitIds)) {
                abort(403, 'Anda hanya dapat membuat siaran pada unit tugas Anda sendiri.');
            }
        }

        $now = now();
        $displayUntil = null;
        if (!empty($validated['display_days'])) {
            $displayUntil = $now->copy()->addDays((int) $validated['display_days']);
        }

        $announcementId = DB::table('announcements')->insertGetId([
            'sender_id'     => $user->id,
            'scope'         => $validated['scope'],
            'unit_id'       => $validated['scope'] === 'unit' ? $validated['unit_id'] : null,
            'category'      => $validated['category'],
            'body'          => $validated['body'],
            'is_urgent'     => $request->boolean('is_urgent'),
            'publish_at'    => $now,
            'display_until' => $displayUntil,
            'status'        => 'published',
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        // Fan-out pembuatan record penerima (announcement_recipients)
        // Jika scope campus -> seluruh staf aktif
        // Jika scope unit -> seluruh staf pada unit tersebut
        if ($validated['scope'] === 'campus') {
            $recipientUserIds = User::where('user_type', 'staff')
                ->where('status', 'active')
                ->pluck('id');
        } else {
            $recipientUserIds = DB::table('user_units')
                ->join('users', 'users.id', '=', 'user_units.user_id')
                ->where('user_units.unit_id', $validated['unit_id'])
                ->where('users.status', 'active')
                ->whereNull('user_units.end_date')
                ->pluck('users.id')
                ->unique();
        }

        $recipientsData = [];
        foreach ($recipientUserIds as $recId) {
            $recipientsData[] = [
                'announcement_id' => $announcementId,
                'user_id'         => $recId,
                'delivered_at'    => $now,
                'read_at'         => ($recId == $user->id) ? $now : null, // Pembuat otomatis ditandai membaca
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        if (!empty($recipientsData)) {
            DB::table('announcement_recipients')->insert($recipientsData);
        }

        return redirect()->route('announcements.index')
            ->with('success', 'Siaran pengumuman berhasil dikirimkan ke seluruh target penerima.');
    }

    /**
     * Tandai pengumuman sudah dibaca oleh pengguna saat ini.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();

        DB::table('announcement_recipients')->updateOrInsert(
            [
                'announcement_id' => $id,
                'user_id'         => $user->id,
            ],
            [
                'read_at'    => now(),
                'updated_at' => now(),
            ]
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    /**
     * Laporan Keterbacaan Presisi (Read Receipts Tracking - COM-02).
     */
    public function readReceipts($id)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $announcement = DB::table('announcements')
            ->join('users', 'users.id', '=', 'announcements.sender_id')
            ->leftJoin('units', 'units.id', '=', 'announcements.unit_id')
            ->select(
                'announcements.*',
                'users.name as sender_name',
                'users.job_title as sender_title',
                'units.code as unit_code',
                'units.name as unit_name'
            )
            ->where('announcements.id', $id)
            ->first();

        if (!$announcement) {
            abort(404, 'Pengumuman tidak ditemukan.');
        }

        // Cek hak akses melihat read receipts
        if (!$user->can('announcement.readreceipt.view') && $announcement->sender_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk melihat laporan keterbacaan siaran ini.');
        }

        if ($user->hasRole('kepala_sekolah') && $announcement->scope === 'unit' && !in_array($announcement->unit_id, $userUnitIds)) {
            abort(403, 'Kepala Sekolah hanya dapat memantau keterbacaan siaran unit miliknya.');
        }

        // Ambil daftar penerima yang sudah membaca & belum membaca
        $recipients = DB::table('announcement_recipients')
            ->join('users', 'users.id', '=', 'announcement_recipients.user_id')
            ->leftJoin('user_units', function ($j) {
                $j->on('user_units.user_id', '=', 'users.id')
                  ->whereNull('user_units.end_date');
            })
            ->leftJoin('units', 'units.id', '=', 'user_units.unit_id')
            ->select(
                'users.id',
                'users.nip',
                'users.name',
                'users.job_title',
                'announcement_recipients.read_at',
                'announcement_recipients.delivered_at',
                DB::raw('GROUP_CONCAT(units.code SEPARATOR ", ") as units_list')
            )
            ->where('announcement_recipients.announcement_id', $id)
            ->groupBy('users.id', 'users.nip', 'users.name', 'users.job_title', 'announcement_recipients.read_at', 'announcement_recipients.delivered_at')
            ->orderByRaw('announcement_recipients.read_at IS NULL')
            ->orderBy('announcement_recipients.read_at', 'desc')
            ->orderBy('users.name')
            ->get();

        $totalRecipients = $recipients->count();
        $readCount = $recipients->whereNotNull('read_at')->count();
        $unreadCount = $totalRecipients - $readCount;
        $readPercentage = $totalRecipients > 0 ? round(($readCount / $totalRecipients) * 100, 1) : 0;

        return view('announcements.read_receipts', compact(
            'announcement', 'recipients', 'totalRecipients', 'readCount', 'unreadCount', 'readPercentage'
        ));
    }
}
