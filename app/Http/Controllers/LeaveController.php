<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    /**
     * Daftar perizinan pegawai & status berjenjang (LV-06 & LV-07).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $statusFilter = $request->get('status', 'all');
        $unitFilter   = $request->get('unit', 'all');

        $query = DB::table('leave_requests')
            ->join('users', 'users.id', '=', 'leave_requests.user_id')
            ->join('units', 'units.id', '=', 'leave_requests.unit_id')
            ->join('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
            ->select(
                'leave_requests.*',
                'users.name as applicant_name',
                'users.nip as applicant_nip',
                'users.job_title as applicant_title',
                'units.code as unit_code',
                'units.name as unit_name',
                'leave_types.name as leave_type_name',
                'leave_types.code as leave_type_code'
            )
            ->whereNull('leave_requests.deleted_at');

        // Batasi visibilitas berdasarkan wewenang:
        if ($user->hasRole('koordinator')) {
            // Koordinator memantau seluruh kampus
        } elseif ($user->hasRole('kepala_sekolah') || $user->hasRole('tata_usaha')) {
            // Kepala Sekolah & TU memantau unit tugas mereka
            $query->whereIn('leave_requests.unit_id', $userUnitIds);
        } else {
            // Guru/Satpam/Pelaksana hanya melihat pengajuan milik sendiri
            $query->where('leave_requests.user_id', $user->id);
        }

        if ($statusFilter !== 'all') {
            $query->where('leave_requests.status', $statusFilter);
        }

        if ($unitFilter !== 'all') {
            $query->where('units.code', $unitFilter);
        }

        $leaveRequests = $query->orderByDesc('leave_requests.created_at')->get();

        // Ambil daftar guru pengganti (substitute) untuk perizinan ini
        $leaveIds = $leaveRequests->pluck('id')->toArray();
        $substitutes = DB::table('substitute_assignments')
            ->join('users', 'users.id', '=', 'substitute_assignments.substitute_user_id')
            ->whereIn('substitute_assignments.leave_request_id', $leaveIds)
            ->select('substitute_assignments.*', 'users.name as substitute_name')
            ->get()
            ->keyBy('leave_request_id');

        // Rekap ringkas ketidakhadiran hari ini (LV-07)
        $today = now()->toDateString();
        $absentToday = DB::table('leave_requests')
            ->join('users', 'users.id', '=', 'leave_requests.user_id')
            ->join('units', 'units.id', '=', 'leave_requests.unit_id')
            ->join('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
            ->where('leave_requests.status', 'approved')
            ->where('leave_requests.start_date', '<=', $today)
            ->where('leave_requests.end_date', '>=', $today)
            ->whereNull('leave_requests.deleted_at')
            ->select('users.name', 'users.nip', 'units.code as unit_code', 'leave_types.name as leave_name', 'leave_requests.reason')
            ->get();

        return view('leaves.index', compact('leaveRequests', 'substitutes', 'absentToday', 'statusFilter', 'unitFilter', 'user'));
    }

    /**
     * Form pengajuan izin baru (LV-01, LV-02, LV-03).
     */
    public function create()
    {
        $user = Auth::user();
        $user->load('units');

        if (!$user->can('leave.request')) {
            abort(403, 'Akun Anda tidak memiliki hak mengajukan perizinan.');
        }

        $leaveTypes = DB::table('leave_types')->where('is_active', true)->get();
        $userUnits = $user->units;

        return view('leaves.create', compact('leaveTypes', 'userUnits', 'user'));
    }

    /**
     * Simpan pengajuan izin & susun langkah approval berjenjang (Bab II Bagian 6).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $user->load('units');

        $validated = $request->validate([
            'leave_type_id'   => ['required', 'exists:leave_types,id'],
            'unit_id'         => ['required', 'exists:units,id'],
            'start_date'      => ['required', 'date'],
            'end_date'        => ['required', 'date', 'after_or_equal:start_date'],
            'reason'          => ['required', 'string', 'max:500'],
            'substitute_plan' => ['nullable', 'string', 'max:500'],
            'attachment'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'attachment.max'          => 'Ukuran berkas lampiran maksimal 5 MB.',
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end   = Carbon::parse($validated['end_date']);
        $totalDays = $start->diffInDays($end) + 1;

        // Cek apakah pemohon adalah Kepala Sekolah (izin pimpinan unit langsung ke Koordinator)
        $isKepsek = $user->hasRole('kepala_sekolah');
        $initialStatus = $isKepsek ? 'pending_coordinator' : 'pending_unit';

        $now = now();
        $leaveId = DB::table('leave_requests')->insertGetId([
            'user_id'          => $user->id,
            'unit_id'          => $validated['unit_id'],
            'leave_type_id'    => $validated['leave_type_id'],
            'start_date'       => $validated['start_date'],
            'end_date'         => $validated['end_date'],
            'total_days'       => $totalDays,
            'reason'           => $validated['reason'],
            'substitute_plan'  => $validated['substitute_plan'] ?? null,
            'status'           => $initialStatus,
            'submitted_at'     => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        // Simpan lampiran berkas jika ada (polimorfik attachments)
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments/leaves', 'public');

            DB::table('attachments')->insert([
                'attachable_type' => 'leave_requests',
                'attachable_id'   => $leaveId,
                'purpose'         => 'surat_dokter',
                'disk'            => 'public',
                'path'            => $path,
                'original_name'   => $file->getClientOriginalName(),
                'mime'            => $file->getClientMimeType(),
                'size_bytes'      => $file->getSize(),
                'uploaded_by'     => $user->id,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }

        // Susun langkah berjenjang di tabel approvals (Bab II Bagian 6)
        if ($isKepsek) {
            // Izin Kepala Sekolah: hanya 1 langkah langsung ke Koordinator
            DB::table('approvals')->insert([
                'approvable_type' => 'leave_requests',
                'approvable_id'   => $leaveId,
                'step_order'      => 1,
                'step_role'       => 'coordinator',
                'status'          => 'pending',
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        } else {
            // Izin Staf/Guru: Langkah 1 Kepala Sekolah -> Langkah 2 Koordinator Kampus
            DB::table('approvals')->insert([
                [
                    'approvable_type' => 'leave_requests',
                    'approvable_id'   => $leaveId,
                    'step_order'      => 1,
                    'step_role'       => 'unit_head',
                    'status'          => 'pending',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ],
                [
                    'approvable_type' => 'leave_requests',
                    'approvable_id'   => $leaveId,
                    'step_order'      => 2,
                    'step_role'       => 'coordinator',
                    'status'          => 'pending',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ],
            ]);
        }

        return redirect()->route('leaves.index')->with('success', 'Pengajuan izin berhasil dikirimkan ke pimpinan.');
    }

    /**
     * Tampilkan detail pengajuan & kontrol persetujuan / penugasan inval.
     */
    public function show($id)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $leave = DB::table('leave_requests')
            ->join('users', 'users.id', '=', 'leave_requests.user_id')
            ->join('units', 'units.id', '=', 'leave_requests.unit_id')
            ->join('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
            ->select(
                'leave_requests.*',
                'users.name as applicant_name',
                'users.nip as applicant_nip',
                'users.job_title as applicant_title',
                'users.staff_category as applicant_category',
                'units.code as unit_code',
                'units.name as unit_name',
                'leave_types.name as leave_type_name',
                'leave_types.requires_attachment'
            )
            ->where('leave_requests.id', $id)
            ->first();

        if (!$leave) {
            abort(404, 'Data perizinan tidak ditemukan.');
        }

        // Cek izin akses
        if (!$user->hasRole(['koordinator', 'kepala_sekolah', 'tata_usaha']) && $leave->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak melihat perizinan ini.');
        }

        // Ambil riwayat approval
        $approvals = DB::table('approvals')
            ->leftJoin('users', 'users.id', '=', 'approvals.approver_id')
            ->leftJoin('delegations', 'delegations.id', '=', 'approvals.via_delegation_id')
            ->where('approvals.approvable_type', 'leave_requests')
            ->where('approvals.approvable_id', $id)
            ->select(
                'approvals.*',
                'users.name as approver_name',
                'delegations.reason as delegation_reason'
            )
            ->orderBy('approvals.step_order')
            ->get();

        // Ambil lampiran berkas
        $attachment = DB::table('attachments')
            ->where('attachable_type', 'leave_requests')
            ->where('attachable_id', $id)
            ->first();

        // Ambil penugasan guru inval jika ada
        $substitute = DB::table('substitute_assignments')
            ->leftJoin('users as sub', 'sub.id', '=', 'substitute_assignments.substitute_user_id')
            ->leftJoin('users as asg', 'asg.id', '=', 'substitute_assignments.assigned_by')
            ->where('substitute_assignments.leave_request_id', $id)
            ->select(
                'substitute_assignments.*',
                'sub.name as substitute_name',
                'sub.job_title as substitute_title',
                'asg.name as assigned_by_name'
            )
            ->first();

        // Daftar guru di unit ini untuk pilihan inval (LV-05)
        $availableTeachers = [];
        if ($user->hasRole('kepala_sekolah') && in_array($leave->unit_id, $userUnitIds)) {
            $availableTeachers = DB::table('user_units')
                ->join('users', 'users.id', '=', 'user_units.user_id')
                ->where('user_units.unit_id', $leave->unit_id)
                ->where('users.id', '!=', $leave->user_id)
                ->where('users.status', 'active')
                ->whereNull('user_units.end_date')
                ->select('users.id', 'users.name', 'users.nip', 'users.job_title')
                ->get();
        }

        // Tentukan apakah user saat ini dapat melakukan tindakan approval
        $canApproveUnitHead = $user->hasRole('kepala_sekolah') 
            && in_array($leave->unit_id, $userUnitIds) 
            && $leave->status === 'pending_unit';

        $canApproveCoordinator = $user->hasRole('koordinator') 
            && $leave->status === 'pending_coordinator';

        return view('leaves.show', compact(
            'leave', 'approvals', 'attachment', 'substitute', 'availableTeachers',
            'canApproveUnitHead', 'canApproveCoordinator', 'user'
        ));
    }

    /**
     * Proses Validasi Kepala Sekolah (Langkah 1) & Penunjukan Inval (LV-04 & LV-05).
     */
    public function approveUnit(Request $request, $id)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $leave = DB::table('leave_requests')->where('id', $id)->first();
        if (!$leave || !in_array($leave->unit_id, $userUnitIds) || !$user->hasRole('kepala_sekolah')) {
            abort(403);
        }

        $decision = $request->input('decision'); // approve or reject
        $note = $request->input('note');
        $now = now();

        if ($decision === 'approve') {
            // Update langkah 1 approvals
            DB::table('approvals')
                ->where('approvable_type', 'leave_requests')
                ->where('approvable_id', $id)
                ->where('step_role', 'unit_head')
                ->update([
                    'approver_id' => $user->id,
                    'status'      => 'approved',
                    'note'        => $note,
                    'decided_at'  => $now,
                    'updated_at'  => $now,
                ]);

            // Jika ada penunjukan guru inval (LV-05)
            $substituteId = $request->input('substitute_user_id');
            if ($substituteId) {
                DB::table('substitute_assignments')->updateOrInsert(
                    ['leave_request_id' => $id],
                    [
                        'substitute_user_id' => $substituteId,
                        'assigned_by'        => $user->id,
                        'class_subject_info' => $request->input('class_subject_info'),
                        'instructions'       => $request->input('instructions'),
                        'status'             => 'assigned',
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ]
                );
            }

            // Maju ke status pending_coordinator
            DB::table('leave_requests')->where('id', $id)->update([
                'status'     => 'pending_coordinator',
                'updated_at' => $now,
            ]);

            $msg = 'Pengajuan izin telah divalidasi oleh Kepala Sekolah dan diteruskan ke Koordinator Kampus.';
        } else {
            // Tolak di langkah unit
            DB::table('approvals')
                ->where('approvable_type', 'leave_requests')
                ->where('approvable_id', $id)
                ->where('step_role', 'unit_head')
                ->update([
                    'approver_id' => $user->id,
                    'status'      => 'rejected',
                    'note'        => $note,
                    'decided_at'  => $now,
                    'updated_at'  => $now,
                ]);

            DB::table('leave_requests')->where('id', $id)->update([
                'status'           => 'rejected',
                'final_decided_at' => $now,
                'updated_at'       => $now,
            ]);

            $msg = 'Pengajuan izin telah ditolak pada tingkat unit.';
        }

        return redirect()->route('leaves.show', $id)->with('success', $msg);
    }

    /**
     * Proses Pengesahan Akhir Koordinator Kampus (Langkah 2 - Final Approval).
     */
    public function approveCoordinator(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('koordinator')) {
            abort(403);
        }

        $decision = $request->input('decision'); // approve or reject
        $note = $request->input('note');
        $now = now();

        if ($decision === 'approve') {
            DB::table('approvals')
                ->where('approvable_type', 'leave_requests')
                ->where('approvable_id', $id)
                ->where('step_role', 'coordinator')
                ->update([
                    'approver_id' => $user->id,
                    'status'      => 'approved',
                    'note'        => $note,
                    'decided_at'  => $now,
                    'updated_at'  => $now,
                ]);

            DB::table('leave_requests')->where('id', $id)->update([
                'status'           => 'approved',
                'final_decided_at' => $now,
                'updated_at'       => $now,
            ]);

            $msg = 'Pengesahan akhir izin telah disetujui resmi oleh Koordinator Kampus.';
        } else {
            DB::table('approvals')
                ->where('approvable_type', 'leave_requests')
                ->where('approvable_id', $id)
                ->where('step_role', 'coordinator')
                ->update([
                    'approver_id' => $user->id,
                    'status'      => 'rejected',
                    'note'        => $note,
                    'decided_at'  => $now,
                    'updated_at'  => $now,
                ]);

            DB::table('leave_requests')->where('id', $id)->update([
                'status'           => 'rejected',
                'final_decided_at' => $now,
                'updated_at'       => $now,
            ]);

            $msg = 'Pengajuan izin telah ditolak oleh Koordinator Kampus.';
        }

        return redirect()->route('leaves.show', $id)->with('success', $msg);
    }

    /**
     * Konfirmasi penerimaan tugas pengganti (Inval Acknowledgment - LV-05).
     */
    public function acknowledgeSubstitute(Request $request, $id)
    {
        $user = Auth::user();

        DB::table('substitute_assignments')
            ->where('id', $id)
            ->where('substitute_user_id', $user->id)
            ->update([
                'status'          => 'acknowledged',
                'acknowledged_at' => now(),
                'updated_at'      => now(),
            ]);

        return back()->with('success', 'Anda telah mengonfirmasi penerimaan tugas guru pengganti (inval).');
    }
}
