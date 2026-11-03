<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FacilityController extends Controller
{
    /**
     * Katalog fasilitas & daftar peminjaman (FC-01, FC-02, FC-08, FC-10).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $tab = $request->get('tab', 'bookings'); // bookings or catalog
        $statusFilter = $request->get('status', 'all');

        // Master Fasilitas
        $facilitiesQuery = DB::table('facilities')
            ->join('facility_categories', 'facility_categories.id', '=', 'facilities.category_id')
            ->leftJoin('units', 'units.id', '=', 'facilities.unit_id')
            ->select(
                'facilities.*',
                'facility_categories.name as category_name',
                'units.code as unit_code'
            )
            ->whereNull('facilities.deleted_at');

        if ($user->user_type === 'external') {
            $facilitiesQuery->where('facilities.visibility', 'public');
        }

        $facilities = $facilitiesQuery->orderBy('facilities.name')->get();

        // Daftar Booking
        $bookingsQuery = DB::table('facility_bookings')
            ->join('facilities', 'facilities.id', '=', 'facility_bookings.facility_id')
            ->join('users', 'users.id', '=', 'facility_bookings.requester_id')
            ->leftJoin('units', 'units.id', '=', 'facilities.unit_id')
            ->select(
                'facility_bookings.*',
                'facilities.name as facility_name',
                'facilities.scope as facility_scope',
                'units.code as unit_code',
                'users.name as requester_name',
                'users.user_type as requester_type',
                'users.organization_name'
            )
            ->whereNull('facility_bookings.deleted_at');

        // Akses booking:
        // - Koordinator: melihat semua
        // - TU: melihat semua untuk persiapan lapangan & screening
        // - Satpam/Pelaksana: melihat semua untuk persiapan gerbang & ruang
        // - Kepsek: melihat fasilitas unitnya
        // - Guru/Eksternal: melihat miliknya sendiri
        if ($user->hasRole(['koordinator', 'tata_usaha', 'satpam_pelaksana'])) {
            // Melihat semua
        } elseif ($user->hasRole('kepala_sekolah')) {
            $bookingsQuery->where(function ($q) use ($user, $userUnitIds) {
                $q->whereIn('facilities.unit_id', $userUnitIds)
                  ->orWhere('facilities.scope', 'campus')
                  ->orWhere('facility_bookings.requester_id', $user->id);
            });
        } else {
            $bookingsQuery->where('facility_bookings.requester_id', $user->id);
        }

        if ($statusFilter !== 'all') {
            $bookingsQuery->where('facility_bookings.status', $statusFilter);
        }

        $bookings = $bookingsQuery->orderByDesc('facility_bookings.start_at')->get();

        return view('facilities.index', compact('facilities', 'bookings', 'tab', 'statusFilter', 'user'));
    }

    /**
     * Form pengajuan peminjaman fasilitas (FC-04 & FC-05).
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $selectedFacilityId = $request->get('facility_id');

        $facilitiesQuery = DB::table('facilities')
            ->where('status', 'active')
            ->whereNull('deleted_at');

        if ($user->user_type === 'external') {
            $facilitiesQuery->where('visibility', 'public');
        }

        $facilities = $facilitiesQuery->orderBy('name')->get();

        return view('facilities.create', compact('facilities', 'selectedFacilityId', 'user'));
    }

    /**
     * Simpan reservasi dengan validasi bentrok real-time (FC-05 & FC-06).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'facility_id'   => ['required', 'exists:facilities,id'],
            'start_at'      => ['required', 'date'],
            'end_at'        => ['required', 'date', 'after:start_at'],
            'participants'  => ['nullable', 'integer', 'min:1'],
            'purpose'       => ['required', 'string', 'max:500'],
            'surat_file'    => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
        ], [
            'end_at.after'  => 'Waktu selesai peminjaman harus lebih akhir dari waktu mulai.',
        ]);

        $facility = DB::table('facilities')->where('id', $validated['facility_id'])->first();

        // Cek maintenance mode
        if ($facility->status === 'maintenance') {
            return back()->withInput()->withErrors([
                'facility_id' => 'Fasilitas sedang dalam pemeliharaan (Maintenance Mode) dan tidak dapat dipinjam.'
            ]);
        }

        // Cek batasan umum
        if ($user->user_type === 'external' && $facility->visibility !== 'public') {
            abort(403, 'Fasilitas ini hanya diperuntukkan bagi kalangan internal sekolah.');
        }

        $startAt = $validated['start_at'];
        $endAt   = $validated['end_at'];

        // 1. Cek bentrok dengan reservasi yang disetujui / pending
        $hasBookingConflict = DB::table('facility_bookings')
            ->where('facility_id', $facility->id)
            ->whereIn('status', ['approved', 'pending_screening', 'pending_unit', 'pending_coordinator'])
            ->whereNull('deleted_at')
            ->where(function ($q) use ($startAt, $endAt) {
                $q->where('start_at', '<', $endAt)
                  ->where('end_at', '>', $startAt);
            })
            ->exists();

        if ($hasBookingConflict) {
            return back()->withInput()->withErrors([
                'start_at' => 'Jadwal bentrok: Fasilitas ini sudah dipesan/sedang diproses oleh peminjam lain pada rentang jam tersebut.'
            ]);
        }

        // 2. Cek bentrok dengan agenda yang memblokir (AG-04)
        $hasAgendaConflict = DB::table('agendas')
            ->where('facility_id', $facility->id)
            ->where('blocks_booking', true)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($startAt, $endAt) {
                $q->where('start_at', '<', $endAt)
                  ->where('end_at', '>', $startAt);
            })
            ->exists();

        if ($hasAgendaConflict) {
            return back()->withInput()->withErrors([
                'start_at' => 'Jadwal bentrok: Terdapat agenda resmi kampus/unit yang memblokir fasilitas ini pada jam tersebut.'
            ]);
        }

        // Tentukan alur approval berjenjang berdasarkan pemohon & skala fasilitas
        $isExternal = ($user->user_type === 'external');
        $bookingCode = 'BK' . date('ymd') . strtoupper(Str::random(4));
        $eticketToken = Str::random(40);
        $now = now();

        // Status awal:
        // - Eksternal: wajib pending_screening oleh TU (FC-07)
        // - Internal Fasilitas Unit: pending_unit
        // - Internal Fasilitas Kampus: pending_coordinator
        if ($isExternal) {
            $initialStatus = 'pending_screening';
        } elseif ($facility->scope === 'unit') {
            $initialStatus = 'pending_unit';
        } else {
            $initialStatus = 'pending_coordinator';
        }

        $bookingId = DB::table('facility_bookings')->insertGetId([
            'booking_code'     => $bookingCode,
            'facility_id'      => $facility->id,
            'requester_id'     => $user->id,
            'is_external'      => $isExternal,
            'start_at'         => $startAt,
            'end_at'           => $endAt,
            'participants'     => $validated['participants'] ?? null,
            'purpose'          => $validated['purpose'],
            'status'           => $initialStatus,
            'eticket_token'    => $eticketToken,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        // Simpan lampiran surat permohonan jika ada
        if ($request->hasFile('surat_file')) {
            $path = $request->file('surat_file')->store('attachments/facilities', 'public');
            DB::table('attachments')->insert([
                'attachable_type' => 'facility_bookings',
                'attachable_id'   => $bookingId,
                'purpose'         => 'surat_permohonan',
                'disk'            => 'public',
                'path'            => $path,
                'original_name'   => $request->file('surat_file')->getClientOriginalName(),
                'mime'            => $request->file('surat_file')->getClientMimeType(),
                'size_bytes'      => $request->file('surat_file')->getSize(),
                'uploaded_by'     => $user->id,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }

        // Susun langkah approvals
        $step = 1;
        $approvalsData = [];

        if ($isExternal) {
            $approvalsData[] = [
                'approvable_type' => 'facility_bookings',
                'approvable_id'   => $bookingId,
                'step_order'      => $step++,
                'step_role'       => 'tu_screening',
                'status'          => 'pending',
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        if ($facility->scope === 'unit') {
            $approvalsData[] = [
                'approvable_type' => 'facility_bookings',
                'approvable_id'   => $bookingId,
                'step_order'      => $step++,
                'step_role'       => 'unit_head',
                'status'          => 'pending',
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Langkah akhir selalu Koordinator Kampus
        $approvalsData[] = [
            'approvable_type' => 'facility_bookings',
            'approvable_id'   => $bookingId,
            'step_order'      => $step++,
            'step_role'       => 'coordinator',
            'status'          => 'pending',
            'created_at'      => $now,
            'updated_at'      => $now,
        ];

        DB::table('approvals')->insert($approvalsData);

        return redirect()->route('facilities.index', ['tab' => 'bookings'])
            ->with('success', "Pengajuan peminjaman fasilitas berhasil dikirim dengan Kode Booking: {$bookingCode}");
    }

    /**
     * Detail peminjaman, tracking e-ticket QR, dan form approval.
     */
    public function show($id)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $booking = DB::table('facility_bookings')
            ->join('facilities', 'facilities.id', '=', 'facility_bookings.facility_id')
            ->join('facility_categories', 'facility_categories.id', '=', 'facilities.category_id')
            ->join('users', 'users.id', '=', 'facility_bookings.requester_id')
            ->leftJoin('units', 'units.id', '=', 'facilities.unit_id')
            ->select(
                'facility_bookings.*',
                'facilities.name as facility_name',
                'facilities.scope as facility_scope',
                'facilities.unit_id as facility_unit_id',
                'facilities.capacity as facility_capacity',
                'facilities.rules_text as facility_rules',
                'facility_categories.name as category_name',
                'units.code as unit_code',
                'users.name as requester_name',
                'users.nip as requester_nip',
                'users.phone_wa as requester_phone',
                'users.organization_name'
            )
            ->where('facility_bookings.id', $id)
            ->first();

        if (!$booking) {
            abort(404);
        }

        $approvals = DB::table('approvals')
            ->leftJoin('users', 'users.id', '=', 'approvals.approver_id')
            ->where('approvals.approvable_type', 'facility_bookings')
            ->where('approvals.approvable_id', $id)
            ->select('approvals.*', 'users.name as approver_name')
            ->orderBy('approvals.step_order')
            ->get();

        $attachment = DB::table('attachments')
            ->where('attachable_type', 'facility_bookings')
            ->where('attachable_id', $id)
            ->first();

        // Otorisasi tindakan:
        $canScreenTU = $user->hasRole('tata_usaha') && $booking->status === 'pending_screening';
        $canApproveUnit = $user->hasRole('kepala_sekolah') && in_array($booking->facility_unit_id, $userUnitIds) && $booking->status === 'pending_unit';
        $canApproveCoordinator = $user->hasRole('koordinator') && $booking->status === 'pending_coordinator';

        return view('facilities.show', compact(
            'booking', 'approvals', 'attachment', 'canScreenTU', 'canApproveUnit', 'canApproveCoordinator', 'user'
        ));
    }

    /**
     * Tindakan Screening TU (FC-07).
     */
    public function actionScreenTU(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('tata_usaha')) abort(403);

        $decision = $request->input('decision'); // pass or return
        $note = $request->input('note');
        $now = now();

        $booking = DB::table('facility_bookings')
            ->join('facilities', 'facilities.id', '=', 'facility_bookings.facility_id')
            ->where('facility_bookings.id', $id)
            ->select('facility_bookings.*', 'facilities.scope as facility_scope')
            ->first();

        if ($decision === 'pass') {
            DB::table('approvals')
                ->where('approvable_type', 'facility_bookings')
                ->where('approvable_id', $id)
                ->where('step_role', 'tu_screening')
                ->update([
                    'approver_id' => $user->id,
                    'status'      => 'approved',
                    'note'        => $note ?? 'Dokumen diverifikasi lengkap.',
                    'decided_at'  => $now,
                    'updated_at'  => $now,
                ]);

            // Jika fasilitas unit -> ke pending_unit, jika kampus -> ke pending_coordinator
            $nextStatus = ($booking->facility_scope === 'unit') ? 'pending_unit' : 'pending_coordinator';
            DB::table('facility_bookings')->where('id', $id)->update([
                'status'     => $nextStatus,
                'updated_at' => $now,
            ]);

            $msg = 'Dokumen lolos screening TU dan diteruskan ke pimpinan.';
        } else {
            DB::table('approvals')
                ->where('approvable_type', 'facility_bookings')
                ->where('approvable_id', $id)
                ->where('step_role', 'tu_screening')
                ->update([
                    'approver_id' => $user->id,
                    'status'      => 'rejected',
                    'note'        => $note ?? 'Dokumen dikembalikan karena belum lengkap.',
                    'decided_at'  => $now,
                    'updated_at'  => $now,
                ]);

            DB::table('facility_bookings')->where('id', $id)->update([
                'status'           => 'rejected',
                'rejection_reason' => $note,
                'updated_at'       => $now,
            ]);

            $msg = 'Pengajuan dikembalikan/ditolak pada tahap screening TU.';
        }

        return redirect()->route('facilities.show', $id)->with('success', $msg);
    }

    /**
     * Tindakan Persetujuan Unit Head (Kepala Sekolah).
     */
    public function actionApproveUnit(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('kepala_sekolah')) abort(403);

        $decision = $request->input('decision');
        $note = $request->input('note');
        $now = now();

        if ($decision === 'approve') {
            DB::table('approvals')
                ->where('approvable_type', 'facility_bookings')
                ->where('approvable_id', $id)
                ->where('step_role', 'unit_head')
                ->update([
                    'approver_id' => $user->id,
                    'status'      => 'approved',
                    'note'        => $note,
                    'decided_at'  => $now,
                    'updated_at'  => $now,
                ]);

            DB::table('facility_bookings')->where('id', $id)->update([
                'status'     => 'pending_coordinator',
                'updated_at' => $now,
            ]);

            $msg = 'Peminjaman divalidasi Kepala Sekolah dan diteruskan ke Koordinator Kampus.';
        } else {
            DB::table('approvals')
                ->where('approvable_type', 'facility_bookings')
                ->where('approvable_id', $id)
                ->where('step_role', 'unit_head')
                ->update([
                    'approver_id' => $user->id,
                    'status'      => 'rejected',
                    'note'        => $note,
                    'decided_at'  => $now,
                    'updated_at'  => $now,
                ]);

            DB::table('facility_bookings')->where('id', $id)->update([
                'status'           => 'rejected',
                'rejection_reason' => $note,
                'updated_at'       => $now,
            ]);

            $msg = 'Peminjaman ditolak oleh Kepala Sekolah.';
        }

        return redirect()->route('facilities.show', $id)->with('success', $msg);
    }

    /**
     * Tindakan Persetujuan Akhir Koordinator Kampus.
     */
    public function actionApproveCoordinator(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('koordinator')) abort(403);

        $decision = $request->input('decision');
        $note = $request->input('note');
        $now = now();

        if ($decision === 'approve') {
            DB::table('approvals')
                ->where('approvable_type', 'facility_bookings')
                ->where('approvable_id', $id)
                ->where('step_role', 'coordinator')
                ->update([
                    'approver_id' => $user->id,
                    'status'      => 'approved',
                    'note'        => $note,
                    'decided_at'  => $now,
                    'updated_at'  => $now,
                ]);

            DB::table('facility_bookings')->where('id', $id)->update([
                'status'      => 'approved',
                'approved_at' => $now,
                'updated_at'  => $now,
            ]);

            $msg = 'Peminjaman fasilitas telah disahkan resmi oleh Koordinator Kampus. E-ticket QR telah aktif!';
        } else {
            DB::table('approvals')
                ->where('approvable_type', 'facility_bookings')
                ->where('approvable_id', $id)
                ->where('step_role', 'coordinator')
                ->update([
                    'approver_id' => $user->id,
                    'status'      => 'rejected',
                    'note'        => $note,
                    'decided_at'  => $now,
                    'updated_at'  => $now,
                ]);

            DB::table('facility_bookings')->where('id', $id)->update([
                'status'           => 'rejected',
                'rejection_reason' => $note,
                'updated_at'       => $now,
            ]);

            $msg = 'Peminjaman fasilitas ditolak oleh Koordinator Kampus.';
        }

        return redirect()->route('facilities.show', $id)->with('success', $msg);
    }
}
