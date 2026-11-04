<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Services\WebPushService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FacilityController extends Controller
{
    /**
     * Katalog fasilitas, jadwal peminjaman, dan kalender ketersediaan (FC-01 s.d FC-10).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $tab = $request->get('tab', 'bookings'); // bookings, calendar, catalog
        $statusFilter = $request->get('status', 'all');
        $unitFilter = $request->get('unit', 'all');

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

        if ($unitFilter !== 'all') {
            if ($unitFilter === 'campus') {
                $facilitiesQuery->where('facilities.scope', 'campus');
            } else {
                $facilitiesQuery->where('units.code', $unitFilter);
            }
        }

        $facilities = $facilitiesQuery->orderBy('facilities.name')->get();

        // Daftar Booking
        $bookingsQuery = DB::table('facility_bookings')
            ->join('facilities', 'facilities.id', '=', 'facility_bookings.facility_id')
            ->join('facility_categories', 'facility_categories.id', '=', 'facilities.category_id')
            ->join('users', 'users.id', '=', 'facility_bookings.requester_id')
            ->leftJoin('units', 'units.id', '=', 'facilities.unit_id')
            ->select(
                'facility_bookings.*',
                'facilities.name as facility_name',
                'facilities.scope as facility_scope',
                'facility_categories.name as category_name',
                'units.code as unit_code',
                'users.name as requester_name',
                'users.user_type as requester_type',
                'users.organization_name'
            )
            ->whereNull('facility_bookings.deleted_at');

        // Hak akses filter booking
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

        // Data Calendar Slots (7 Hari Ke Depan untuk Visual Slot Ketersediaan)
        $calendarStartDate = Carbon::today();
        $calendarDays = [];
        for ($i = 0; $i < 7; $i++) {
            $calendarDays[] = $calendarStartDate->copy()->addDays($i);
        }

        $activeBookingsForCalendar = DB::table('facility_bookings')
            ->whereIn('status', ['approved', 'pending_screening', 'pending_unit', 'pending_coordinator'])
            ->whereNull('deleted_at')
            ->where('start_at', '>=', Carbon::today())
            ->where('end_at', '<=', Carbon::today()->addDays(8))
            ->get();

        return view('facilities.index', compact(
            'facilities', 'bookings', 'tab', 'statusFilter', 'unitFilter', 'calendarDays', 'activeBookingsForCalendar', 'user'
        ));
    }

    /**
     * Form pengajuan peminjaman cerdas (Smart Booking Form - PRD Migrasi 5.2).
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $selectedFacilityId = $request->get('facility_id');

        $facilitiesQuery = DB::table('facilities')
            ->join('facility_categories', 'facility_categories.id', '=', 'facilities.category_id')
            ->select('facilities.*', 'facility_categories.name as category_name')
            ->where('facilities.status', 'active')
            ->whereNull('facilities.deleted_at');

        if ($user->user_type === 'external') {
            $facilitiesQuery->where('facilities.visibility', 'public');
        }

        $facilities = $facilitiesQuery->orderBy('facilities.name')->get();
        $availableItems = DB::table('facility_items')->where('is_active', true)->get();

        return view('facilities.create', compact('facilities', 'selectedFacilityId', 'availableItems', 'user'));
    }

    /**
     * Simpan reservasi dengan validasi bentrok atomik & buffer persiapan (PRD Migrasi 3 & 5.2).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'facility_id'    => ['required', 'exists:facilities,id'],
            'event_name'     => ['nullable', 'string', 'max:255'],
            'destination'    => ['nullable', 'string', 'max:255'],
            'driver_needed'  => ['nullable', 'boolean'],
            'pic_name'       => ['required', 'string', 'max:150'],
            'pic_phone'      => ['required', 'string', 'max:50'],
            'start_at'       => ['required', 'date'],
            'end_at'         => ['required', 'date', 'after:start_at'],
            'participants'   => ['nullable', 'integer', 'min:1'],
            'purpose'        => ['required', 'string', 'max:600'],
            'items'          => ['nullable', 'array'],
            'items.*'        => ['nullable', 'integer', 'min:1'],
            'surat_file'     => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
        ], [
            'end_at.after'   => 'Waktu selesai peminjaman harus lebih akhir dari waktu mulai.',
            'pic_name.required' => 'Nama Penanggung Jawab (PIC) wajib diisi.',
            'pic_phone.required' => 'Nomor WhatsApp PIC wajib diisi untuk koordinasi lapangan.',
        ]);

        $facility = DB::table('facilities')
            ->join('facility_categories', 'facility_categories.id', '=', 'facilities.category_id')
            ->select('facilities.*', 'facility_categories.name as category_name')
            ->where('facilities.id', $validated['facility_id'])
            ->first();

        if ($facility->status === 'maintenance') {
            return back()->withInput()->withErrors([
                'facility_id' => 'Fasilitas sedang dalam perbaikan (Maintenance) dan belum dapat dipinjam.'
            ]);
        }

        if ($user->user_type === 'external' && $facility->visibility !== 'public') {
            abort(403, 'Fasilitas ini hanya diperuntukkan bagi kalangan internal sekolah.');
        }

        $startAt = Carbon::parse($validated['start_at']);
        $endAt   = Carbon::parse($validated['end_at']);

        // Jeda buffer pembersihan/persiapan 15 menit (PRD Migrasi 3)
        $bufferMinutes = 15;
        $checkStart = $startAt->copy()->subMinutes($bufferMinutes);
        $checkEnd   = $endAt->copy()->addMinutes($bufferMinutes);

        // Eksekusi atomik database locking (SELECT FOR UPDATE)
        $bookingResult = DB::transaction(function () use ($user, $validated, $facility, $startAt, $endAt, $checkStart, $checkEnd, $request) {
            
            // 1. Cek bentrok atomik dengan pengajuan aktif
            $conflict = DB::table('facility_bookings')
                ->where('facility_id', $facility->id)
                ->whereIn('status', ['approved', 'pending_screening', 'pending_unit', 'pending_coordinator'])
                ->whereNull('deleted_at')
                ->where(function ($q) use ($checkStart, $checkEnd) {
                    $q->where('start_at', '<', $checkEnd)
                      ->where('end_at', '>', $checkStart);
                })
                ->lockForUpdate()
                ->first();

            if ($conflict) {
                return [
                    'status' => 'error',
                    'message' => 'Jadwal bentrok: Fasilitas ini sudah dipesan pada rentang jam tersebut (termasuk jeda buffer persiapan 15 menit).'
                ];
            }

            // 2. Cek bentrok dengan agenda resmi yang memblokir
            $agendaConflict = DB::table('agendas')
                ->where('facility_id', $facility->id)
                ->where('blocks_booking', true)
                ->whereNull('deleted_at')
                ->where(function ($q) use ($startAt, $endAt) {
                    $q->where('start_at', '<', $endAt)
                      ->where('end_at', '>', $startAt);
                })
                ->first();

            if ($agendaConflict) {
                return [
                    'status' => 'error',
                    'message' => 'Jadwal bentrok: Terdapat agenda resmi kampus/unit yang memblokir fasilitas ini.'
                ];
            }

            $isExternal = ($user->user_type === 'external');
            $isVehicle = (strtolower($facility->category_name) === 'kendaraan');
            $bookingCode = 'BK' . date('ymd') . strtoupper(Str::random(4));
            $eticketToken = Str::random(40);
            $now = now();

            // Format Nomor Registrasi Surat Resmi: {Urut}/SIPF/YPL-DLT/{BulanRomawi}/{Tahun}
            $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
            $romanMonth = $romanMonths[$now->month] ?? 'XI';
            $nextSeq = (DB::table('facility_bookings')->whereYear('created_at', $now->year)->count()) + 1;
            $letterNumber = sprintf('%03d/SIPF/YPL-DLT/%s/%d', $nextSeq, $romanMonth, $now->year);

            // Tentukan status awal approval bertingkat (PRD Migrasi 5.3):
            // - Eksternal: wajib pending_screening oleh TU
            // - Fasilitas Kendaraan & Fasilitas Skala Kampus/Umum: Langsung Tier 2 (pending_coordinator)
            // - Fasilitas Unit (misal: Aula SMA): Tier 1 (pending_unit)
            if ($isExternal) {
                $initialStatus = 'pending_screening';
            } elseif ($isVehicle || $facility->scope === 'campus') {
                $initialStatus = 'pending_coordinator';
            } else {
                $initialStatus = 'pending_unit';
            }

            $bookingId = DB::table('facility_bookings')->insertGetId([
                'booking_code'     => $bookingCode,
                'facility_id'      => $facility->id,
                'event_name'       => $validated['event_name'] ?? $validated['purpose'],
                'pic_name'         => $validated['pic_name'],
                'pic_phone'        => $validated['pic_phone'],
                'destination'      => $validated['destination'] ?? null,
                'driver_needed'    => $request->boolean('driver_needed'),
                'requester_id'     => $user->id,
                'is_external'      => $isExternal,
                'start_at'         => $startAt,
                'end_at'           => $endAt,
                'participants'     => $validated['participants'] ?? null,
                'purpose'          => $validated['purpose'],
                'status'           => $initialStatus,
                'eticket_token'    => $eticketToken,
                'letter_number'    => $letterNumber,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            // Simpan barang sarpras tambahan yang dipilih
            if (!empty($validated['items'])) {
                $itemsData = [];
                foreach ($validated['items'] as $itemId => $qty) {
                    if ((int) $qty > 0) {
                        $itemsData[] = [
                            'booking_id' => $bookingId,
                            'item_id'    => $itemId,
                            'quantity'   => (int) $qty,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
                if (!empty($itemsData)) {
                    DB::table('facility_booking_items')->insert($itemsData);
                }
            }

            // Simpan lampiran surat pemohon jika ada
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

            // Susun alur persetujuan bertingkat (Two-Tier Approval)
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

            if (!$isVehicle && $facility->scope === 'unit') {
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

            // Otorisasi akhir selalu Koordinator Sarpras Yayasan
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

            return [
                'status'      => 'success',
                'booking_id'  => $bookingId,
                'booking_code'=> $bookingCode,
                'facility'    => $facility->name
            ];
        });

        if ($bookingResult['status'] === 'error') {
            return back()->withInput()->withErrors([
                'start_at' => $bookingResult['message']
            ]);
        }

        // Trigger Web Push Notification ke Verifikator
        try {
            $pushService = app(WebPushService::class);
            if ($pushService->isConfigured()) {
                $pushService->sendToAll([
                    'title' => 'Permohonan Fasilitas Baru',
                    'body'  => "{$user->name} mengajukan peminjaman: {$bookingResult['facility']}",
                    'url'   => route('facilities.show', $bookingResult['booking_id']),
                ]);
            }
        } catch (\Throwable $e) {}

        return redirect()->route('facilities.index', ['tab' => 'bookings'])
            ->with('success', "Permohonan berhasil dikirim dengan Kode Booking: {$bookingResult['booking_code']}");
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

        $items = DB::table('facility_booking_items')
            ->join('facility_items', 'facility_items.id', '=', 'facility_booking_items.item_id')
            ->where('facility_booking_items.booking_id', $id)
            ->select('facility_items.name', 'facility_booking_items.quantity')
            ->get();

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

        $canScreenTU = $user->hasRole('tata_usaha') && $booking->status === 'pending_screening';
        $canApproveUnit = $user->hasRole('kepala_sekolah') && in_array($booking->facility_unit_id, $userUnitIds) && $booking->status === 'pending_unit';
        $canApproveCoordinator = $user->hasRole('koordinator') && $booking->status === 'pending_coordinator';

        return view('facilities.show', compact(
            'booking', 'items', 'approvals', 'attachment', 'canScreenTU', 'canApproveUnit', 'canApproveCoordinator', 'user'
        ));
    }

    /**
     * Surat Izin Resmi Peminjaman Fasilitas Ber-Kop Yayasan (PRD Migrasi 5.4).
     */
    public function letter($id)
    {
        $user = Auth::user();

        $booking = DB::table('facility_bookings')
            ->join('facilities', 'facilities.id', '=', 'facility_bookings.facility_id')
            ->join('facility_categories', 'facility_categories.id', '=', 'facilities.category_id')
            ->join('users', 'users.id', '=', 'facility_bookings.requester_id')
            ->leftJoin('units', 'units.id', '=', 'facilities.unit_id')
            ->select(
                'facility_bookings.*',
                'facilities.name as facility_name',
                'facilities.scope as facility_scope',
                'facilities.capacity as facility_capacity',
                'facilities.rules_text as facility_rules',
                'facility_categories.name as category_name',
                'units.name as unit_name',
                'units.code as unit_code',
                'users.name as requester_name',
                'users.nip as requester_nip',
                'users.job_title as requester_title',
                'users.phone_wa as requester_phone',
                'users.organization_name'
            )
            ->where('facility_bookings.id', $id)
            ->first();

        if (!$booking) {
            abort(404);
        }

        $items = DB::table('facility_booking_items')
            ->join('facility_items', 'facility_items.id', '=', 'facility_booking_items.item_id')
            ->where('facility_booking_items.booking_id', $id)
            ->select('facility_items.name', 'facility_booking_items.quantity')
            ->get();

        $coordinatorApproval = DB::table('approvals')
            ->leftJoin('users', 'users.id', '=', 'approvals.approver_id')
            ->where('approvals.approvable_type', 'facility_bookings')
            ->where('approvals.approvable_id', $id)
            ->where('approvals.step_role', 'coordinator')
            ->select('approvals.*', 'users.name as approver_name')
            ->first();

        return view('facilities.letter', compact('booking', 'items', 'coordinatorApproval', 'user'));
    }

    /**
     * Screening TU.
     */
    public function actionScreenTU(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('tata_usaha')) abort(403);

        $decision = $request->input('decision');
        $note = $request->input('note');
        $now = now();

        if ($decision === 'return' && empty($note)) {
            return back()->withErrors(['note' => 'Catatan alasan wajib diisi saat mengembalikan permohonan.']);
        }

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
                    'note'        => $note,
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
     * Validasi Kepala Sekolah Unit (Tier 1).
     */
    public function actionApproveUnit(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('kepala_sekolah')) abort(403);

        $decision = $request->input('decision');
        $note = $request->input('note');
        $now = now();

        if ($decision === 'reject' && empty($note)) {
            return back()->withErrors(['note' => 'Catatan alasan penolakan wajib diisi (Mandatory Rejection Note).']);
        }

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
     * Otorisasi Akhir Koordinator Sarpras Kampus (Tier 2).
     */
    public function actionApproveCoordinator(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('koordinator')) abort(403);

        $decision = $request->input('decision');
        $note = $request->input('note');
        $driverName = $request->input('driver_name');
        $now = now();

        if ($decision === 'reject' && empty($note)) {
            return back()->withErrors(['note' => 'Catatan alasan penolakan wajib diisi (Mandatory Rejection Note).']);
        }

        $booking = DB::table('facility_bookings')->where('id', $id)->first();

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

            $updateData = [
                'status'      => 'approved',
                'approved_at' => $now,
                'updated_at'  => $now,
            ];

            if (!empty($driverName)) {
                $updateData['driver_name'] = $driverName;
            }

            DB::table('facility_bookings')->where('id', $id)->update($updateData);

            // Notifikasi Web Push ke Pemohon
            try {
                $pushService = app(WebPushService::class);
                $requester = User::find($booking->requester_id);
                if ($pushService->isConfigured() && $requester) {
                    $pushService->sendToUser($requester, [
                        'title' => 'Izin Fasilitas Disetujui!',
                        'body'  => "Permohonan fasilitas ({$booking->booking_code}) telah disahkan. E-Ticket & Surat Izin resmi telah aktif.",
                        'url'   => route('facilities.show', $id),
                    ]);
                }
            } catch (\Throwable $e) {}

            $msg = 'Peminjaman fasilitas telah disahkan resmi oleh Koordinator Kampus. E-Ticket & E-Surat Izin Resmi telah terbit!';
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

            // Notifikasi Web Push ke Pemohon
            try {
                $pushService = app(WebPushService::class);
                $requester = User::find($booking->requester_id);
                if ($pushService->isConfigured() && $requester) {
                    $pushService->sendToUser($requester, [
                        'title' => 'Permohonan Fasilitas Ditolak',
                        'body'  => "Permohonan fasilitas ({$booking->booking_code}) tidak disetujui: {$note}",
                        'url'   => route('facilities.show', $id),
                    ]);
                }
            } catch (\Throwable $e) {}

            $msg = 'Peminjaman fasilitas ditolak oleh Koordinator Kampus.';
        }

        return redirect()->route('facilities.show', $id)->with('success', $msg);
    }
}
