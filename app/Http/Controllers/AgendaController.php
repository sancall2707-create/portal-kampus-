<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    /**
     * Tampilkan kalender & daftar agenda (AG-01 & AG-02).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $category = $request->get('category', 'all'); // all, campus, unit, personal
        $view = $request->get('view', 'list'); // list or calendar

        $query = DB::table('agendas')
            ->join('users', 'users.id', '=', 'agendas.created_by')
            ->leftJoin('units', 'units.id', '=', 'agendas.unit_id')
            ->leftJoin('facilities', 'facilities.id', '=', 'agendas.facility_id')
            ->select(
                'agendas.*',
                'users.name as creator_name',
                'users.job_title as creator_title',
                'units.code as unit_code',
                'units.name as unit_name',
                'facilities.name as facility_name'
            )
            ->whereNull('agendas.deleted_at');

        // Batasi visibilitas agenda sesuai peran & unit
        if ($user->hasRole('koordinator')) {
            // Koordinator melihat seluruh agenda kampus & unit
        } else {
            $query->where(function ($q) use ($user, $userUnitIds) {
                $q->where('agendas.category', 'campus')
                  ->orWhere(function ($q2) use ($userUnitIds) {
                      $q2->where('agendas.category', 'unit')
                         ->whereIn('agendas.unit_id', $userUnitIds);
                  })
                  ->orWhere(function ($q3) use ($user) {
                      $q3->where('agendas.category', 'personal')
                         ->where('agendas.created_by', $user->id);
                  });
            });
        }

        if ($category !== 'all') {
            $query->where('agendas.category', $category);
        }

        $agendas = $query->orderBy('agendas.start_at', 'asc')->get();

        $canCreate = $user->can('agenda.create');

        return view('agendas.index', compact('agendas', 'category', 'view', 'canCreate', 'user'));
    }

    /**
     * Tampilkan form pembuatan agenda baru (AG-03).
     */
    public function create()
    {
        $user = Auth::user();
        $user->load('units');

        if (!$user->can('agenda.create')) {
            abort(403, 'Anda tidak memiliki wewenang untuk membuat agenda.');
        }

        // Unit yang bisa dipilih
        if ($user->hasRole('koordinator')) {
            $availableUnits = Unit::where('is_active', true)->orderBy('sort_order')->get();
        } else {
            $availableUnits = $user->units;
        }

        // Ambil daftar fasilitas untuk opsi peminjaman ruang
        $facilities = DB::table('facilities')
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        // Batas karakter dari app_settings
        $maxChars = DB::table('app_settings')->where('setting_key', 'agenda_max_chars')->value('setting_value') ?? 100;

        return view('agendas.create', compact('availableUnits', 'facilities', 'maxChars', 'user'));
    }

    /**
     * Simpan agenda baru & validasi cegah bentrok (AG-04).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $maxChars = (int) (DB::table('app_settings')->where('setting_key', 'agenda_max_chars')->value('setting_value') ?? 100);

        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:150'],
            'category'       => ['required', 'in:campus,unit,personal'],
            'unit_id'        => ['nullable', 'required_if:category,unit', 'exists:units,id'],
            'facility_id'    => ['nullable', 'exists:facilities,id'],
            'location_text'  => ['nullable', 'string', 'max:150'],
            'start_at'       => ['required', 'date'],
            'end_at'         => ['required', 'date', 'after:start_at'],
            'description'    => ['nullable', 'string', "max:{$maxChars}"],
            'blocks_booking' => ['nullable', 'boolean'],
        ], [
            'end_at.after'    => 'Waktu selesai harus lebih akhir dari waktu mulai.',
            'description.max' => "Rincian agenda tidak boleh melebihi {$maxChars} karakter sesuai aturan sistem.",
        ]);

        // Validasi hak pembuatan sesuai peran
        if ($validated['category'] === 'campus' && !$user->hasRole(['koordinator', 'kepala_sekolah'])) {
            abort(403, 'Hanya pimpinan yang berhak membuat agenda resmi tingkat kampus.');
        }

        if ($validated['category'] === 'unit') {
            if (!$user->hasRole('koordinator') && !in_array($validated['unit_id'], $userUnitIds)) {
                abort(403, 'Anda hanya dapat membuat agenda pada unit tugas Anda sendiri.');
            }
        }

        // Cek apakah agenda menggunakan fasilitas kampus dan meminta blokir reservasi
        $facilityId = $validated['facility_id'] ?? null;
        $blocksBooking = $request->boolean('blocks_booking');

        if ($facilityId && $blocksBooking) {
            $startAt = $validated['start_at'];
            $endAt   = $validated['end_at'];

            // Periksa bentrok dengan agenda pemblokir lain
            $hasAgendaConflict = DB::table('agendas')
                ->where('facility_id', $facilityId)
                ->where('blocks_booking', true)
                ->whereNull('deleted_at')
                ->where(function ($q) use ($startAt, $endAt) {
                    $q->where('start_at', '<', $endAt)
                      ->where('end_at', '>', $startAt);
                })
                ->exists();

            if ($hasAgendaConflict) {
                return back()->withInput()->withErrors([
                    'facility_id' => 'Fasilitas/ruang ini sudah diagendakan dan diblokir oleh acara lain pada rentang waktu yang sama.'
                ]);
            }

            // Periksa bentrok dengan peminjaman fasilitas yang sudah disetujui
            $hasBookingConflict = DB::table('facility_bookings')
                ->where('facility_id', $facilityId)
                ->whereIn('status', ['approved', 'pending_coordinator', 'pending_unit'])
                ->whereNull('deleted_at')
                ->where(function ($q) use ($startAt, $endAt) {
                    $q->where('start_at', '<', $endAt)
                      ->where('end_at', '>', $startAt);
                })
                ->exists();

            if ($hasBookingConflict) {
                return back()->withInput()->withErrors([
                    'facility_id' => 'Terdapat peminjaman fasilitas yang sedang aktif / disetujui pada jadwal tersebut.'
                ]);
            }
        }

        DB::table('agendas')->insert([
            'created_by'     => $user->id,
            'title'          => $validated['title'],
            'category'       => $validated['category'],
            'unit_id'        => $validated['category'] === 'unit' ? $validated['unit_id'] : null,
            'facility_id'    => $facilityId,
            'location_text'  => $validated['location_text'] ?? null,
            'start_at'       => $validated['start_at'],
            'end_at'         => $validated['end_at'],
            'description'    => $validated['description'] ?? null,
            'blocks_booking' => $blocksBooking,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()->route('agendas.index')
            ->with('success', 'Agenda kegiatan berhasil dijadwalkan ke kalender sistem.');
    }
}
