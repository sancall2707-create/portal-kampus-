<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Models\Unit;
use App\Services\WebPushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * Daftar grup kepanitiaan sesuai cakupan visibilitas peran (GR-01 s.d. GR-04).
     *
     * Visibilitas:
     * - Koordinator  : seluruh grup lintas unit (group.supervise)
     * - Kepala Unit  : grup skala kampus + grup di unitnya sendiri
     * - Guru/TU/dll  : grup yang ia ajukan atau yang ia menjadi anggota
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $statusFilter = $request->get('status', 'all');

        $query = DB::table('groups')
            ->leftJoin('units', 'units.id', '=', 'groups.unit_id')
            ->join('users as req', 'req.id', '=', 'groups.requested_by')
            ->leftJoin('users as rev', 'rev.id', '=', 'groups.reviewed_by')
            ->select(
                'groups.*',
                'units.code as unit_code',
                'req.name as requester_name',
                'rev.name as reviewer_name',
                DB::raw('(SELECT COUNT(*) FROM group_members WHERE group_members.group_id = groups.id) as member_count')
            )
            ->whereNull('groups.deleted_at');

        if ($user->hasPermissionTo('group.supervise')) {
            // Koordinator memantau seluruh grup tanpa batas unit
        } elseif ($user->hasRole('kepala_sekolah')) {
            $query->where(function ($q) use ($userUnitIds) {
                $q->whereNull('groups.unit_id')
                  ->orWhereIn('groups.unit_id', $userUnitIds);
            });
        } else {
            $memberGroupIds = DB::table('group_members')
                ->where('user_id', $user->id)
                ->pluck('group_id')
                ->toArray();

            $query->where(function ($q) use ($user, $memberGroupIds) {
                $q->where('groups.requested_by', $user->id);
                if (! empty($memberGroupIds)) {
                    $q->orWhereIn('groups.id', $memberGroupIds);
                }
            });
        }

        if ($statusFilter !== 'all') {
            $query->where('groups.status', $statusFilter);
        }

        $groups = $query->orderByRaw("FIELD(groups.status, 'pending', 'active', 'rejected', 'archived')")
            ->orderByDesc('groups.created_at')
            ->get();

        // Tandai grup mana yang pengguna ini kelola (admin grup) atau ikuti
        $myMemberships = DB::table('group_members')
            ->where('user_id', $user->id)
            ->pluck('member_role', 'group_id')
            ->toArray();

        $pendingCount = DB::table('groups')
            ->where('status', 'pending')
            ->whereNull('deleted_at')
            ->when(! $user->hasPermissionTo('group.supervise'), function ($q) use ($userUnitIds) {
                $q->whereIn('unit_id', $userUnitIds);
            })
            ->count();

        // Dropdown unit hanya menawarkan unit yang benar-benar boleh diajukan
        // pengguna ini, agar tidak memilih unit lalu ditolak validasi.
        $units = $user->hasPermissionTo('group.supervise')
            ? Unit::where('is_active', true)->orderBy('sort_order')->get()
            : Unit::where('is_active', true)
                ->whereIn('id', $userUnitIds)
                ->orderBy('sort_order')
                ->get();

        return view('groups.index', compact(
            'groups', 'myMemberships', 'statusFilter', 'units', 'user', 'pendingCount'
        ));
    }

    /**
     * Ajukan pembentukan grup kepanitiaan baru (GR-01).
     * Guru/TU mengajukan, status awal 'pending' menunggu validasi Kepala Unit.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user->hasPermissionTo('group.request')) {
            abort(403, 'Anda tidak memiliki wewenang mengajukan pembentukan grup.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:120'],
            'category'    => ['required', 'in:kepanitiaan,bidang_kerja,proyek,lainnya'],
            'scope'       => ['required', 'in:campus,unit'],
            'unit_id'     => ['nullable', 'exists:units,id'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required'     => 'Nama kepanitiaan wajib diisi.',
            'name.max'          => 'Nama kepanitiaan maksimal 120 karakter.',
            'category.required' => 'Kategori grup wajib dipilih.',
            'description.max'   => 'Deskripsi maksimal 500 karakter.',
        ]);

        $user->load('units');

        // Grup skala unit wajib terikat pada unit penempatan pengaju
        $unitId = null;
        if ($validated['scope'] === 'unit') {
            $unitId = $validated['unit_id'] ?? $user->units->first()?->id;

            if (! $unitId) {
                return back()->withErrors([
                    'unit_id' => 'Unit sekolah wajib dipilih untuk grup berskala unit.',
                ])->withInput();
            }

            if (! $user->hasPermissionTo('group.supervise')
                && ! $user->units->pluck('id')->contains($unitId)) {
                return back()->withErrors([
                    'unit_id' => 'Anda hanya dapat mengajukan grup pada unit penempatan Anda sendiri.',
                ])->withInput();
            }
        }

        // Cegah duplikasi nama grup aktif pada cakupan yang sama
        $duplicate = DB::table('groups')
            ->where('name', $validated['name'])
            ->where('scope', $validated['scope'])
            ->where('unit_id', $unitId)
            ->whereIn('status', ['pending', 'active'])
            ->whereNull('deleted_at')
            ->exists();

        if ($duplicate) {
            return back()->withErrors([
                'name' => 'Grup dengan nama tersebut sudah ada atau masih menunggu persetujuan.',
            ])->withInput();
        }

        $now = now();

        // Koordinator mengajukan sekaligus mengesahkan grupnya sendiri
        $isAutoApproved = $user->hasPermissionTo('group.approve');

        $groupId = DB::table('groups')->insertGetId([
            'name'          => $validated['name'],
            'scope'         => $validated['scope'],
            'category'      => $validated['category'],
            'unit_id'       => $unitId,
            'requested_by'  => $user->id,
            'status'        => $isAutoApproved ? 'active' : 'pending',
            'reviewed_by'   => $isAutoApproved ? $user->id : null,
            'reviewed_at'   => $isAutoApproved ? $now : null,
            'description'   => $validated['description'] ?? null,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        // Pengaju otomatis menjadi pengelola (admin) grup
        DB::table('group_members')->insert([
            'group_id'    => $groupId,
            'user_id'     => $user->id,
            'member_role' => 'admin',
            'added_by'    => $user->id,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        // Notifikasi ke validator (Kepala Unit terkait, atau Koordinator untuk skala kampus)
        if (! $isAutoApproved) {
            $this->notifyApprovers($unitId, $validated['name'], $user->name);
        }

        $message = $isAutoApproved
            ? "Grup \"{$validated['name']}\" berhasil dibuat dan langsung aktif."
            : "Pengajuan grup \"{$validated['name']}\" terkirim dan menunggu validasi Kepala Unit.";

        return redirect()->route('groups.index')->with('success', $message);
    }

    /**
     * Ruang kolaborasi grup: daftar anggota dan pengelolaan keanggotaan (GR-03).
     */
    public function show($id)
    {
        $user = Auth::user();
        $user->load('units');

        $group = DB::table('groups')
            ->leftJoin('units', 'units.id', '=', 'groups.unit_id')
            ->join('users as req', 'req.id', '=', 'groups.requested_by')
            ->leftJoin('users as rev', 'rev.id', '=', 'groups.reviewed_by')
            ->select(
                'groups.*',
                'units.code as unit_code',
                'units.name as unit_name',
                'req.name as requester_name',
                'rev.name as reviewer_name'
            )
            ->where('groups.id', $id)
            ->whereNull('groups.deleted_at')
            ->first();

        if (! $group) {
            abort(404, 'Grup tidak ditemukan.');
        }

        $myRole = DB::table('group_members')
            ->where('group_id', $id)
            ->where('user_id', $user->id)
            ->value('member_role');

        $isSupervisor = $user->hasPermissionTo('group.supervise');
        $isUnitHead = $user->hasRole('kepala_sekolah')
            && (is_null($group->unit_id) || $user->units->pluck('id')->contains($group->unit_id));

        // Otorisasi akses: anggota, pengelola, kepala unit terkait, atau koordinator
        if (! $myRole && ! $isSupervisor && ! $isUnitHead) {
            abort(403, 'Anda bukan anggota grup ini.');
        }

        $members = DB::table('group_members')
            ->join('users', 'users.id', '=', 'group_members.user_id')
            ->leftJoin('user_units', function ($join) {
                $join->on('user_units.user_id', '=', 'users.id')
                     ->whereNull('user_units.end_date');
            })
            ->leftJoin('units as mu', 'mu.id', '=', 'user_units.unit_id')
            ->select(
                'group_members.id as membership_id',
                'group_members.member_role',
                'group_members.created_at as joined_at',
                'users.id as user_id',
                'users.name',
                'users.nip',
                'users.job_title',
                DB::raw('GROUP_CONCAT(DISTINCT mu.code ORDER BY mu.sort_order SEPARATOR " & ") as unit_codes')
            )
            ->where('group_members.group_id', $id)
            ->groupBy(
                'group_members.id', 'group_members.member_role', 'group_members.created_at',
                'users.id', 'users.name', 'users.nip', 'users.job_title'
            )
            ->orderByRaw("FIELD(group_members.member_role, 'admin', 'member')")
            ->orderBy('users.name')
            ->get();

        // Kandidat anggota baru: staf aktif yang belum terdaftar di grup ini
        $existingIds = $members->pluck('user_id')->toArray();
        $candidates = collect();

        if ($myRole === 'admin' || $isSupervisor) {
            $candidates = DB::table('users')
                ->leftJoin('user_units', function ($join) {
                    $join->on('user_units.user_id', '=', 'users.id')
                         ->whereNull('user_units.end_date');
                })
                ->leftJoin('units as cu', 'cu.id', '=', 'user_units.unit_id')
                ->select(
                    'users.id', 'users.name', 'users.nip', 'users.job_title',
                    DB::raw('GROUP_CONCAT(DISTINCT cu.code ORDER BY cu.sort_order SEPARATOR " & ") as unit_codes')
                )
                ->where('users.user_type', 'staff')
                ->where('users.status', 'active')
                ->whereNull('users.deleted_at')
                ->when(! empty($existingIds), fn ($q) => $q->whereNotIn('users.id', $existingIds))
                ->groupBy('users.id', 'users.name', 'users.nip', 'users.job_title')
                ->orderBy('users.name')
                ->get();
        }

        $canManage = ($myRole === 'admin' && $group->status === 'active') || $isSupervisor;
        $canReview = $group->status === 'pending' && ($isSupervisor || $isUnitHead)
            && $user->hasPermissionTo('group.approve');

        return view('groups.show', compact(
            'group', 'members', 'candidates', 'myRole', 'canManage', 'canReview', 'user'
        ));
    }

    /**
     * Validasi pengajuan grup oleh Kepala Unit / Koordinator (GR-02).
     */
    public function review(Request $request, $id)
    {
        $user = Auth::user();
        $user->load('units');

        if (! $user->hasPermissionTo('group.approve')) {
            abort(403, 'Anda tidak memiliki wewenang memvalidasi pengajuan grup.');
        }

        $validated = $request->validate([
            'decision'      => ['required', 'in:approve,reject'],
            'reject_reason' => ['required_if:decision,reject', 'nullable', 'string', 'max:300'],
        ], [
            'reject_reason.required_if' => 'Alasan penolakan wajib diisi agar pengaju memahami keputusan.',
        ]);

        $group = DB::table('groups')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $group) {
            abort(404, 'Grup tidak ditemukan.');
        }

        if ($group->status !== 'pending') {
            return back()->with('error', 'Pengajuan grup ini sudah pernah diproses.');
        }

        // Kepala unit hanya boleh memvalidasi grup di unitnya; koordinator bebas
        if (! $user->hasPermissionTo('group.supervise')) {
            $inScope = is_null($group->unit_id) || $user->units->pluck('id')->contains($group->unit_id);
            if (! $inScope) {
                abort(403, 'Grup ini berada di luar cakupan unit Anda.');
            }
        }

        $approved = $validated['decision'] === 'approve';
        $now = now();

        DB::table('groups')->where('id', $id)->update([
            'status'        => $approved ? 'active' : 'rejected',
            'reviewed_by'   => $user->id,
            'reviewed_at'   => $now,
            'reject_reason' => $approved ? null : $validated['reject_reason'],
            'updated_at'    => $now,
        ]);

        // Beri tahu pengaju lewat Web Push
        $this->pushTo(
            $group->requested_by,
            $approved ? 'Pengajuan Grup Disetujui' : 'Pengajuan Grup Ditolak',
            $approved
                ? "Grup \"{$group->name}\" telah disetujui dan kini aktif."
                : "Grup \"{$group->name}\" ditolak: {$validated['reject_reason']}",
            route('groups.show', $id)
        );

        return redirect()->route('groups.show', $id)->with(
            'success',
            $approved
                ? "Grup \"{$group->name}\" disetujui dan sekarang aktif."
                : "Pengajuan grup \"{$group->name}\" ditolak."
        );
    }

    /**
     * Tambah anggota ke grup aktif (GR-03) — hanya pengelola grup atau koordinator.
     */
    public function addMember(Request $request, $id)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'user_ids'    => ['required', 'array', 'min:1', 'max:50'],
            'user_ids.*'  => ['exists:users,id'],
            'member_role' => ['required', 'in:admin,member'],
        ], [
            'user_ids.required' => 'Pilih minimal satu pegawai untuk ditambahkan.',
            'user_ids.max'      => 'Maksimal 50 anggota dapat ditambahkan sekaligus.',
        ]);

        $group = DB::table('groups')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $group) {
            abort(404, 'Grup tidak ditemukan.');
        }

        if ($group->status !== 'active') {
            return back()->with('error', 'Anggota hanya dapat ditambahkan pada grup berstatus aktif.');
        }

        $this->authorizeManage($id, $group);

        $existingIds = DB::table('group_members')
            ->where('group_id', $id)
            ->pluck('user_id')
            ->toArray();

        $now = now();
        $rows = [];
        foreach ($validated['user_ids'] as $uid) {
            if (in_array((int) $uid, array_map('intval', $existingIds), true)) {
                continue;
            }
            $rows[] = [
                'group_id'    => $id,
                'user_id'     => $uid,
                'member_role' => $validated['member_role'],
                'added_by'    => $user->id,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        if (empty($rows)) {
            return back()->with('error', 'Seluruh pegawai yang dipilih sudah terdaftar di grup ini.');
        }

        DB::table('group_members')->insert($rows);

        // Notifikasi ke anggota baru
        foreach ($rows as $row) {
            $this->pushTo(
                $row['user_id'],
                'Keanggotaan Grup Baru',
                "Anda ditambahkan ke grup \"{$group->name}\" oleh {$user->name}.",
                route('groups.show', $id)
            );
        }

        return redirect()->route('groups.show', $id)
            ->with('success', count($rows) . ' anggota berhasil ditambahkan ke grup.');
    }

    /**
     * Keluarkan anggota dari grup (GR-03).
     */
    public function removeMember($id, $membershipId)
    {
        $group = DB::table('groups')->where('id', $id)->whereNull('deleted_at')->first();
        if (! $group) {
            abort(404, 'Grup tidak ditemukan.');
        }

        $this->authorizeManage($id, $group);

        $membership = DB::table('group_members')
            ->where('id', $membershipId)
            ->where('group_id', $id)
            ->first();

        if (! $membership) {
            abort(404, 'Keanggotaan tidak ditemukan.');
        }

        // Pengaju grup tidak boleh dikeluarkan agar grup tetap bertuan
        if ((int) $membership->user_id === (int) $group->requested_by) {
            return back()->with('error', 'Pengaju grup tidak dapat dikeluarkan dari grupnya sendiri.');
        }

        DB::table('group_members')->where('id', $membershipId)->delete();

        return redirect()->route('groups.show', $id)
            ->with('success', 'Anggota berhasil dikeluarkan dari grup.');
    }

    /**
     * Pastikan pengguna berhak mengelola keanggotaan grup ini.
     */
    private function authorizeManage($groupId, $group): void
    {
        $user = Auth::user();

        if ($user->hasPermissionTo('group.supervise')) {
            return;
        }

        $myRole = DB::table('group_members')
            ->where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->value('member_role');

        if ($myRole !== 'admin') {
            abort(403, 'Hanya pengelola grup yang dapat mengubah keanggotaan.');
        }
    }

    /**
     * Kirim notifikasi push ke para validator pengajuan grup.
     */
    private function notifyApprovers($unitId, string $groupName, string $requesterName): void
    {
        $approverIds = DB::table('users')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                     ->where('model_has_roles.model_type', '=', \App\Models\User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->when($unitId, function ($q) use ($unitId) {
                $q->leftJoin('user_units', function ($join) use ($unitId) {
                    $join->on('user_units.user_id', '=', 'users.id')
                         ->where('user_units.unit_id', '=', $unitId)
                         ->whereNull('user_units.end_date');
                });
                $q->where(function ($sub) {
                    $sub->whereNotNull('user_units.id')
                        ->orWhere('roles.name', 'koordinator');
                });
            })
            ->whereIn('roles.name', ['kepala_sekolah', 'koordinator'])
            ->where('users.status', 'active')
            ->whereNull('users.deleted_at')
            ->distinct()
            ->pluck('users.id');

        foreach ($approverIds as $uid) {
            $this->pushTo(
                $uid,
                'Pengajuan Grup Baru',
                "{$requesterName} mengajukan pembentukan grup \"{$groupName}\".",
                route('groups.index')
            );
        }
    }

    /**
     * Pengiriman push notification yang aman gagal (tidak memutus alur utama).
     */
    private function pushTo($userId, string $title, string $body, string $url): void
    {
        try {
            $push = new WebPushService();
            if (! $push->isConfigured()) {
                return;
            }

            // sendToSubscription menuntut instance model PushSubscription, bukan baris stdClass
            $subs = PushSubscription::where('user_id', $userId)->get();
            foreach ($subs as $sub) {
                $push->sendToSubscription($sub, [
                    'title' => $title,
                    'body'  => $body,
                    'url'   => $url,
                ]);
            }
        } catch (\Throwable $e) {
            // Kegagalan notifikasi tidak boleh membatalkan transaksi grup
            report($e);
        }
    }
}
