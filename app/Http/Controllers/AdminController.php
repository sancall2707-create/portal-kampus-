<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Pusat kendali Koordinator Sekolah: metrik operasional dan ringkasan sistem.
     */
    public function dashboard()
    {
        $this->authorizeAdmin('dashboard.analytics');

        $now = now();
        $monthStart = $now->copy()->startOfMonth();

        // Rekapitulasi akun pengguna
        $userStats = [
            'total'     => DB::table('users')->whereNull('deleted_at')->count(),
            'active'    => DB::table('users')->where('status', 'active')->whereNull('deleted_at')->count(),
            'suspended' => DB::table('users')->where('status', 'suspended')->whereNull('deleted_at')->count(),
            'external'  => DB::table('users')->where('user_type', 'external')->whereNull('deleted_at')->count(),
            'never_login' => DB::table('users')
                ->whereNull('last_login_at')
                ->where('user_type', 'staff')
                ->whereNull('deleted_at')
                ->count(),
        ];

        // Ketidakhadiran hari ini per unit (monitor kehadiran terpadu)
        $absentToday = DB::table('leave_requests')
            ->join('users', 'users.id', '=', 'leave_requests.user_id')
            ->leftJoin('units', 'units.id', '=', 'leave_requests.unit_id')
            ->join('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
            ->select(
                'users.name', 'users.nip', 'units.code as unit_code',
                'leave_types.name as leave_type', 'leave_requests.start_date', 'leave_requests.end_date'
            )
            ->where('leave_requests.status', 'approved')
            ->whereDate('leave_requests.start_date', '<=', $now->toDateString())
            ->whereDate('leave_requests.end_date', '>=', $now->toDateString())
            ->whereNull('leave_requests.deleted_at')
            ->orderBy('units.sort_order')
            ->get();

        // Tren perizinan bulan berjalan per unit
        $leaveByUnit = DB::table('leave_requests')
            ->leftJoin('units', 'units.id', '=', 'leave_requests.unit_id')
            ->select('units.code as unit_code', DB::raw('COUNT(*) as total'))
            ->where('leave_requests.created_at', '>=', $monthStart)
            ->whereNull('leave_requests.deleted_at')
            ->groupBy('units.code', 'units.sort_order')
            ->orderBy('units.sort_order')
            ->get();

        // Intensitas peminjaman fasilitas
        $facilityUsage = DB::table('facility_bookings')
            ->join('facilities', 'facilities.id', '=', 'facility_bookings.facility_id')
            ->select('facilities.name', DB::raw('COUNT(*) as total'))
            ->whereNull('facility_bookings.deleted_at')
            ->groupBy('facilities.id', 'facilities.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // Antrean kerja yang menunggu keputusan koordinator
        $queue = [
            'leaves' => DB::table('leave_requests')
                ->whereIn('status', ['pending_unit', 'pending_coordinator'])
                ->whereNull('deleted_at')
                ->count(),
            'bookings' => DB::table('facility_bookings')
                ->whereIn('status', ['pending_screening', 'pending_unit', 'pending_coordinator'])
                ->whereNull('deleted_at')
                ->count(),
            'groups' => DB::table('groups')
                ->where('status', 'pending')
                ->whereNull('deleted_at')
                ->count(),
            'tickets' => DB::table('tickets')
                ->whereIn('status', ['open', 'assigned', 'in_progress'])
                ->whereNull('deleted_at')
                ->count(),
        ];

        // Operasional lapangan
        $operational = [
            'tasks_open'    => DB::table('tasks')->whereIn('status', ['pending', 'in_progress'])->whereNull('deleted_at')->count(),
            'panic_month'   => DB::table('panic_alerts')->where('created_at', '>=', $monthStart)->count(),
            'guests_today'  => DB::table('guest_logs')->whereDate('check_in_at', $now->toDateString())->count(),
            'letters_month' => DB::table('letters')->where('created_at', '>=', $monthStart)->whereNull('deleted_at')->count(),
        ];

        // Aktivitas login terakhir untuk evaluasi engagement
        $recentLogins = DB::table('login_logs')
            ->leftJoin('users', 'users.id', '=', 'login_logs.user_id')
            ->select('login_logs.*', 'users.name', 'users.nip')
            ->orderByDesc('login_logs.logged_in_at')
            ->limit(10)
            ->get();

        // Akun pasif (tidak pernah login atau lama tidak aktif)
        $passiveUsers = DB::table('users')
            ->leftJoin('user_units', function ($join) {
                $join->on('user_units.user_id', '=', 'users.id')->whereNull('user_units.end_date');
            })
            ->leftJoin('units', 'units.id', '=', 'user_units.unit_id')
            ->select('users.id', 'users.name', 'users.nip', 'users.last_login_at',
                DB::raw('GROUP_CONCAT(DISTINCT units.code SEPARATOR " & ") as unit_codes'))
            ->where('users.user_type', 'staff')
            ->where('users.status', 'active')
            ->whereNull('users.deleted_at')
            ->where(function ($q) use ($now) {
                $q->whereNull('users.last_login_at')
                  ->orWhere('users.last_login_at', '<', $now->copy()->subDays(30));
            })
            ->groupBy('users.id', 'users.name', 'users.nip', 'users.last_login_at')
            ->orderByRaw('users.last_login_at IS NULL DESC, users.last_login_at ASC')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'userStats', 'absentToday', 'leaveByUnit', 'facilityUsage',
            'queue', 'operational', 'recentLogins', 'passiveUsers'
        ));
    }

    /**
     * Manajemen akun pegawai: daftar, filter, dan pencarian.
     */
    public function users(Request $request)
    {
        $this->authorizeAdmin('user.manage');

        $search = trim((string) $request->get('q', ''));
        $roleFilter = $request->get('role', 'all');
        $unitFilter = $request->get('unit', 'all');
        $statusFilter = $request->get('status', 'all');

        $query = DB::table('users')
            ->leftJoin('user_units', function ($join) {
                $join->on('user_units.user_id', '=', 'users.id')->whereNull('user_units.end_date');
            })
            ->leftJoin('units', 'units.id', '=', 'user_units.unit_id')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                     ->where('model_has_roles.model_type', '=', User::class);
            })
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select(
                'users.id', 'users.nip', 'users.name', 'users.email', 'users.user_type',
                'users.job_title', 'users.staff_category', 'users.status', 'users.last_login_at',
                DB::raw('GROUP_CONCAT(DISTINCT roles.name SEPARATOR ", ") as role_names'),
                DB::raw('GROUP_CONCAT(DISTINCT units.code ORDER BY units.sort_order SEPARATOR " & ") as unit_codes'),
                DB::raw('MAX(user_units.position) as unit_position')
            )
            ->whereNull('users.deleted_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.nip', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter !== 'all') {
            $query->where('roles.name', $roleFilter);
        }

        if ($unitFilter !== 'all') {
            $query->where('units.code', $unitFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('users.status', $statusFilter);
        }

        $users = $query
            ->groupBy(
                'users.id', 'users.nip', 'users.name', 'users.email', 'users.user_type',
                'users.job_title', 'users.staff_category', 'users.status', 'users.last_login_at'
            )
            ->orderBy('users.nip')
            ->paginate(20)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();
        $units = Unit::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.users', compact(
            'users', 'roles', 'units', 'search', 'roleFilter', 'unitFilter', 'statusFilter'
        ));
    }

    /**
     * Tambah akun pegawai baru.
     */
    public function storeUser(Request $request)
    {
        $this->authorizeAdmin('user.manage');

        $validated = $request->validate([
            'nip'            => ['required', 'string', 'max:30', 'unique:users,nip'],
            'name'           => ['required', 'string', 'max:150'],
            'email'          => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8'],
            'role'           => ['required', 'exists:roles,name'],
            'unit_ids'       => ['required', 'array', 'min:1'],
            'unit_ids.*'     => ['exists:units,id'],
            'job_title'      => ['nullable', 'string', 'max:120'],
            'staff_category' => ['required', 'in:pimpinan,guru,tata_usaha,satpam,pelaksana'],
            'position'       => ['required', 'in:kepala_sekolah,wakil_kepala,guru,tata_usaha,satpam,pelaksana'],
            'phone_wa'       => ['nullable', 'string', 'max:25'],
        ], [
            'nip.unique'       => 'NIP tersebut sudah terdaftar pada akun lain.',
            'password.min'     => 'Kata sandi minimal 8 karakter.',
            'unit_ids.required' => 'Pilih minimal satu unit penempatan.',
            'position.required' => 'Jabatan struktural di unit wajib dipilih.',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'user_type'      => 'staff',
                'nip'            => $validated['nip'],
                'name'           => $validated['name'],
                'email'          => $validated['email'] ?? null,
                'password'       => $validated['password'],
                'job_title'      => $validated['job_title'] ?? null,
                'staff_category' => $validated['staff_category'],
                'phone_wa'       => $validated['phone_wa'] ?? null,
                'status'         => 'active',
            ]);

            $user->assignRole($validated['role']);

            $now = now();
            foreach ($validated['unit_ids'] as $i => $unitId) {
                DB::table('user_units')->insert([
                    'user_id'    => $user->id,
                    'unit_id'    => $unitId,
                    // Kolom position adalah enum jabatan struktural, bukan teks bebas
                    'position'   => $validated['position'],
                    'is_primary' => $i === 0,
                    'start_date' => $now->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        return redirect()->route('admin.users')
            ->with('success', "Akun {$validated['name']} ({$validated['nip']}) berhasil dibuat.");
    }

    /**
     * Perbarui data akun pegawai, peran, dan penempatan unit.
     */
    public function updateUser(Request $request, $id)
    {
        $this->authorizeAdmin('user.manage');

        $target = User::findOrFail($id);

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:150'],
            'nip'            => ['required', 'string', 'max:30', Rule::unique('users', 'nip')->ignore($target->id)],
            'email'          => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($target->id)],
            'role'           => ['required', 'exists:roles,name'],
            'unit_ids'       => ['required', 'array', 'min:1'],
            'unit_ids.*'     => ['exists:units,id'],
            'job_title'      => ['nullable', 'string', 'max:120'],
            'staff_category' => ['required', 'in:pimpinan,guru,tata_usaha,satpam,pelaksana'],
            'position'       => ['required', 'in:kepala_sekolah,wakil_kepala,guru,tata_usaha,satpam,pelaksana'],
            'phone_wa'       => ['nullable', 'string', 'max:25'],
        ]);

        DB::transaction(function () use ($target, $validated) {
            $target->update([
                'name'           => $validated['name'],
                'nip'            => $validated['nip'],
                'email'          => $validated['email'] ?? null,
                'job_title'      => $validated['job_title'] ?? null,
                'staff_category' => $validated['staff_category'],
                'phone_wa'       => $validated['phone_wa'] ?? null,
            ]);

            $target->syncRoles([$validated['role']]);

            // Tutup penempatan lama lalu catat penempatan baru (mutasi terdokumentasi)
            $now = now();
            DB::table('user_units')
                ->where('user_id', $target->id)
                ->whereNull('end_date')
                ->whereNotIn('unit_id', $validated['unit_ids'])
                ->update(['end_date' => $now->toDateString(), 'updated_at' => $now]);

            $existing = DB::table('user_units')
                ->where('user_id', $target->id)
                ->whereNull('end_date')
                ->pluck('unit_id')
                ->toArray();

            foreach ($validated['unit_ids'] as $i => $unitId) {
                if (in_array((int) $unitId, array_map('intval', $existing), true)) {
                    continue;
                }
                DB::table('user_units')->insert([
                    'user_id'    => $target->id,
                    'unit_id'    => $unitId,
                    'position'   => $validated['position'],
                    'is_primary' => $i === 0,
                    'start_date' => $now->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        return redirect()->route('admin.users')
            ->with('success', "Data akun {$validated['name']} berhasil diperbarui.");
    }

    /**
     * Reset kata sandi pengguna yang kehilangan akses.
     */
    public function resetPassword(Request $request, $id)
    {
        $this->authorizeAdmin('user.manage');

        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:8'],
        ], [
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
        ]);

        $target = User::findOrFail($id);
        $target->update(['password' => $validated['new_password']]);

        DB::table('activity_log')->insert([
            'log_name'     => 'admin',
            'description'  => "Reset kata sandi akun {$target->nip}",
            'subject_type' => User::class,
            'subject_id'   => $target->id,
            'event'        => 'password_reset',
            'causer_type'  => User::class,
            'causer_id'    => Auth::id(),
            'properties'   => json_encode(['nip' => $target->nip]),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return back()->with('success', "Kata sandi akun {$target->name} berhasil direset.");
    }

    /**
     * Aktifkan / nonaktifkan (suspend) akun pegawai.
     */
    public function toggleStatus($id)
    {
        $this->authorizeAdmin('user.manage');

        $target = User::findOrFail($id);

        if ((int) $target->id === (int) Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $newStatus = $target->status === 'active' ? 'suspended' : 'active';
        $target->update(['status' => $newStatus]);

        DB::table('activity_log')->insert([
            'log_name'     => 'admin',
            'description'  => "Ubah status akun {$target->nip} menjadi {$newStatus}",
            'subject_type' => User::class,
            'subject_id'   => $target->id,
            'event'        => 'status_change',
            'causer_type'  => User::class,
            'causer_id'    => Auth::id(),
            'properties'   => json_encode(['from' => $target->status, 'to' => $newStatus]),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $label = $newStatus === 'active' ? 'diaktifkan kembali' : 'dinonaktifkan';

        return back()->with('success', "Akun {$target->name} berhasil {$label}.");
    }

    /**
     * Delegasi wewenang persetujuan sementara (Plt / Acting Role).
     */
    public function delegations(Request $request)
    {
        $this->authorizeAdmin('delegation.manage');

        $delegations = DB::table('delegations')
            ->join('users as dor', 'dor.id', '=', 'delegations.delegator_id')
            ->join('users as dee', 'dee.id', '=', 'delegations.delegate_id')
            ->leftJoin('units', 'units.id', '=', 'delegations.unit_id')
            ->select(
                'delegations.*',
                'dor.name as delegator_name', 'dor.nip as delegator_nip',
                'dee.name as delegate_name', 'dee.nip as delegate_nip',
                'units.code as unit_code'
            )
            ->orderByDesc('delegations.is_active')
            ->orderByDesc('delegations.start_at')
            ->get();

        $staff = DB::table('users')
            ->leftJoin('user_units', function ($join) {
                $join->on('user_units.user_id', '=', 'users.id')->whereNull('user_units.end_date');
            })
            ->leftJoin('units', 'units.id', '=', 'user_units.unit_id')
            ->select('users.id', 'users.name', 'users.nip',
                DB::raw('GROUP_CONCAT(DISTINCT units.code SEPARATOR " & ") as unit_codes'))
            ->where('users.user_type', 'staff')
            ->where('users.status', 'active')
            ->whereNull('users.deleted_at')
            ->groupBy('users.id', 'users.name', 'users.nip')
            ->orderBy('users.name')
            ->get();

        $units = Unit::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.delegations', compact('delegations', 'staff', 'units'));
    }

    /**
     * Terbitkan delegasi wewenang baru.
     */
    public function storeDelegation(Request $request)
    {
        $this->authorizeAdmin('delegation.manage');

        $validated = $request->validate([
            'delegator_id' => ['required', 'exists:users,id'],
            'delegate_id'  => ['required', 'exists:users,id', 'different:delegator_id'],
            'unit_id'      => ['nullable', 'exists:units,id'],
            'scope'        => ['required', 'in:approval_leave,approval_facility,academic_assignment,academic_monitoring,all'],
            'start_at'     => ['required', 'date'],
            'end_at'       => ['required', 'date', 'after:start_at'],
            'reason'       => ['required', 'string', 'max:300'],
        ], [
            'delegate_id.different' => 'Penerima delegasi tidak boleh sama dengan pemberi delegasi.',
            'end_at.after'          => 'Tanggal berakhir harus setelah tanggal mulai.',
            'reason.required'       => 'Alasan delegasi wajib diisi untuk keperluan audit.',
        ]);

        // Cegah delegasi tumpang tindih pada cakupan yang sama
        $overlap = DB::table('delegations')
            ->where('delegator_id', $validated['delegator_id'])
            ->where('scope', $validated['scope'])
            ->where('is_active', true)
            ->where('start_at', '<=', $validated['end_at'])
            ->where('end_at', '>=', $validated['start_at'])
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'scope' => 'Sudah ada delegasi aktif dengan cakupan sama pada rentang waktu tersebut.',
            ])->withInput();
        }

        $now = now();
        DB::table('delegations')->insert([
            'delegator_id' => $validated['delegator_id'],
            'delegate_id'  => $validated['delegate_id'],
            'unit_id'      => $validated['unit_id'] ?? null,
            'scope'        => $validated['scope'],
            'start_at'     => $validated['start_at'],
            'end_at'       => $validated['end_at'],
            'reason'       => $validated['reason'],
            'is_active'    => true,
            'created_by'   => Auth::id(),
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        return redirect()->route('admin.delegations')
            ->with('success', 'Delegasi wewenang berhasil diterbitkan.');
    }

    /**
     * Cabut delegasi wewenang yang masih aktif.
     */
    public function revokeDelegation($id)
    {
        $this->authorizeAdmin('delegation.manage');

        $updated = DB::table('delegations')
            ->where('id', $id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'updated_at' => now()]);

        if (! $updated) {
            return back()->with('error', 'Delegasi tidak ditemukan atau sudah tidak aktif.');
        }

        return back()->with('success', 'Delegasi wewenang berhasil dicabut.');
    }

    /**
     * Jejak audit aktivitas sistem dan riwayat login.
     */
    public function audit(Request $request)
    {
        $this->authorizeAdmin('audit.view');

        $tab = $request->get('tab', 'activity');

        $activities = DB::table('activity_log')
            ->leftJoin('users', 'users.id', '=', 'activity_log.causer_id')
            ->select('activity_log.*', 'users.name as causer_name', 'users.nip as causer_nip')
            ->orderByDesc('activity_log.created_at')
            ->limit(100)
            ->get();

        $logins = DB::table('login_logs')
            ->leftJoin('users', 'users.id', '=', 'login_logs.user_id')
            ->select('login_logs.*', 'users.name', 'users.nip')
            ->orderByDesc('login_logs.logged_in_at')
            ->limit(100)
            ->get();

        $impersonations = DB::table('impersonation_logs')
            ->join('users as imp', 'imp.id', '=', 'impersonation_logs.impersonator_id')
            ->join('users as tgt', 'tgt.id', '=', 'impersonation_logs.impersonated_id')
            ->select(
                'impersonation_logs.*',
                'imp.name as impersonator_name',
                'tgt.name as impersonated_name', 'tgt.nip as impersonated_nip'
            )
            ->orderByDesc('impersonation_logs.started_at')
            ->limit(50)
            ->get();

        return view('admin.audit', compact('tab', 'activities', 'logins', 'impersonations'));
    }

    /**
     * Masuk sebagai pengguna lain untuk investigasi kendala teknis.
     */
    public function impersonate(Request $request, $id)
    {
        $this->authorizeAdmin('user.impersonate');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:300'],
        ], [
            'reason.required' => 'Alasan simulasi akun wajib diisi untuk keperluan audit.',
        ]);

        $target = User::findOrFail($id);
        $admin = Auth::user();

        if ((int) $target->id === (int) $admin->id) {
            return back()->with('error', 'Tidak dapat menyimulasikan akun Anda sendiri.');
        }

        if ($target->hasPermissionTo('user.impersonate')) {
            return back()->with('error', 'Akun dengan wewenang simulasi tidak dapat disimulasikan.');
        }

        $logId = DB::table('impersonation_logs')->insertGetId([
            'impersonator_id' => $admin->id,
            'impersonated_id' => $target->id,
            'reason'          => $validated['reason'],
            'started_at'      => now(),
            'ip_address'      => $request->ip(),
        ]);

        // Simpan identitas asli agar dapat dipulihkan
        session([
            'impersonator_id'      => $admin->id,
            'impersonation_log_id' => $logId,
        ]);

        Auth::login($target);

        return redirect()->route('dashboard')
            ->with('success', "Mode simulasi aktif sebagai {$target->name}. Gunakan tombol kembali untuk mengakhiri.");
    }

    /**
     * Akhiri mode simulasi dan kembali ke akun koordinator.
     */
    public function stopImpersonate()
    {
        $originalId = session('impersonator_id');
        $logId = session('impersonation_log_id');

        if (! $originalId) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada sesi simulasi yang aktif.');
        }

        if ($logId) {
            DB::table('impersonation_logs')->where('id', $logId)->update(['ended_at' => now()]);
        }

        $original = User::find($originalId);
        session()->forget(['impersonator_id', 'impersonation_log_id']);

        if (! $original) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun asal tidak ditemukan. Silakan masuk kembali.');
        }

        Auth::login($original);

        return redirect()->route('admin.dashboard')->with('success', 'Mode simulasi diakhiri.');
    }

    /**
     * Data induk: unit sekolah, tahun ajaran, semester, dan pengaturan sistem.
     */
    public function masterData()
    {
        $this->authorizeAdmin('masterdata.manage');

        $units = DB::table('units')
            ->leftJoin('user_units', function ($join) {
                $join->on('user_units.unit_id', '=', 'units.id')->whereNull('user_units.end_date');
            })
            ->select('units.*', DB::raw('COUNT(DISTINCT user_units.user_id) as staff_count'))
            ->groupBy('units.id', 'units.code', 'units.name', 'units.is_active', 'units.sort_order', 'units.created_at', 'units.updated_at')
            ->orderBy('units.sort_order')
            ->get();

        $academicYears = DB::table('academic_years')->orderByDesc('start_date')->get();

        $semesters = DB::table('semesters')
            ->join('academic_years', 'academic_years.id', '=', 'semesters.academic_year_id')
            ->select('semesters.*', 'academic_years.name as year_name')
            ->orderByDesc('semesters.start_date')
            ->get();

        $settings = DB::table('app_settings')->orderBy('setting_group')->orderBy('setting_key')->get();

        $leaveTypes = DB::table('leave_types')->orderBy('name')->get();

        return view('admin.master', compact('units', 'academicYears', 'semesters', 'settings', 'leaveTypes'));
    }

    /**
     * Tetapkan tahun ajaran dan semester aktif.
     */
    public function setActivePeriod(Request $request)
    {
        $this->authorizeAdmin('masterdata.manage');

        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_id'      => ['required', 'exists:semesters,id'],
        ]);

        // Pastikan semester benar-benar milik tahun ajaran yang dipilih
        $belongs = DB::table('semesters')
            ->where('id', $validated['semester_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->exists();

        if (! $belongs) {
            return back()->withErrors([
                'semester_id' => 'Semester yang dipilih bukan bagian dari tahun ajaran tersebut.',
            ]);
        }

        DB::transaction(function () use ($validated) {
            $now = now();
            DB::table('academic_years')->update(['is_active' => false, 'updated_at' => $now]);
            DB::table('academic_years')->where('id', $validated['academic_year_id'])
                ->update(['is_active' => true, 'updated_at' => $now]);

            DB::table('semesters')->update(['is_active' => false, 'updated_at' => $now]);
            DB::table('semesters')->where('id', $validated['semester_id'])
                ->update(['is_active' => true, 'updated_at' => $now]);
        });

        return back()->with('success', 'Tahun ajaran dan semester aktif berhasil diperbarui.');
    }

    /**
     * Perbarui pengaturan sistem (app_settings).
     */
    public function updateSetting(Request $request)
    {
        $this->authorizeAdmin('masterdata.manage');

        $validated = $request->validate([
            'setting_key'   => ['required', 'exists:app_settings,setting_key'],
            'setting_value' => ['required', 'string', 'max:500'],
        ]);

        DB::table('app_settings')
            ->where('setting_key', $validated['setting_key'])
            ->update([
                'setting_value' => $validated['setting_value'],
                'updated_at'    => now(),
            ]);

        return back()->with('success', "Pengaturan {$validated['setting_key']} berhasil diperbarui.");
    }

    /**
     * Gerbang otorisasi tunggal untuk seluruh aksi panel admin.
     */
    private function authorizeAdmin(string $permission): void
    {
        $user = Auth::user();

        if (! $user || ! $user->hasPermissionTo($permission)) {
            abort(403, 'Anda tidak memiliki wewenang mengakses panel ini.');
        }
    }
}
