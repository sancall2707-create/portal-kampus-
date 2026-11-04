<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WebPushService;
use Illuminate\Support\Str;

class OperationalController extends Controller
{
    /**
     * Dashboard Operasional Lapangan (Tasks, Tickets, Panic Button, Guest Logs).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();
        $viewTab = $request->get('tab', 'tasks'); // tasks, tickets, panic, guests

        // 1. Direct Tasking
        $tasksQuery = DB::table('tasks')
            ->join('users as creator', 'creator.id', '=', 'tasks.created_by')
            ->leftJoin('units', 'units.id', '=', 'tasks.unit_id')
            ->select('tasks.*', 'creator.name as creator_name', 'units.code as unit_code');

        if ($user->hasRole(['koordinator', 'tata_usaha'])) {
            // Melihat semua
        } elseif ($user->hasRole('kepala_sekolah')) {
            $tasksQuery->whereIn('tasks.unit_id', $userUnitIds);
        } else {
            // Pelaksana/Satpam melihat tugas yang ditugaskan ke dirinya
            $tasksQuery->join('task_assignees', function ($j) use ($user) {
                $j->on('task_assignees.task_id', '=', 'tasks.id')
                  ->where('task_assignees.user_id', $user->id);
            });
        }
        $tasks = $tasksQuery->orderByDesc('tasks.created_at')->get();

        // 2. Tiket Kerusakan
        $ticketsQuery = DB::table('tickets')
            ->join('users as rep', 'rep.id', '=', 'tickets.reporter_id')
            ->join('units', 'units.id', '=', 'tickets.unit_id')
            ->leftJoin('facilities', 'facilities.id', '=', 'tickets.facility_id')
            ->leftJoin('users as tech', 'tech.id', '=', 'tickets.assigned_to')
            ->select('tickets.*', 'rep.name as reporter_name', 'units.code as unit_code', 'facilities.name as facility_name', 'tech.name as technician_name');

        if (!$user->hasRole(['koordinator', 'tata_usaha', 'satpam_pelaksana'])) {
            if ($user->hasRole('kepala_sekolah')) {
                $ticketsQuery->whereIn('tickets.unit_id', $userUnitIds);
            } else {
                $ticketsQuery->where('tickets.reporter_id', $user->id);
            }
        }
        $tickets = $ticketsQuery->orderByDesc('tickets.created_at')->get();

        // 3. Panic Alerts (Peringatan Darurat)
        $panicAlerts = DB::table('panic_alerts')
            ->join('users as trig', 'trig.id', '=', 'panic_alerts.triggered_by')
            ->join('units', 'units.id', '=', 'panic_alerts.unit_id')
            ->select('panic_alerts.*', 'trig.name as trigger_name', 'units.code as unit_code')
            ->orderByDesc('panic_alerts.triggered_at')
            ->limit(20)
            ->get();

        // 4. Buku Tamu (Guest Logs)
        $guestLogs = DB::table('guest_logs')
            ->join('units', 'units.id', '=', 'guest_logs.unit_id')
            ->join('users as satpam', 'satpam.id', '=', 'guest_logs.recorded_by')
            ->select('guest_logs.*', 'units.code as unit_code', 'satpam.name as satpam_name')
            ->orderByDesc('guest_logs.check_in_at')
            ->limit(30)
            ->get();

        return view('operational.index', compact('tasks', 'tickets', 'panicAlerts', 'guestLogs', 'viewTab', 'user'));
    }

    /**
     * Buat Penugasan Langsung (Direct Tasking - OP-01).
     */
    public function storeTask(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('task.assign')) abort(403);

        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:150'],
            'unit_id'      => ['nullable', 'exists:units,id'],
            'assignee_id'  => ['required', 'exists:users,id'],
            'priority'     => ['required', 'in:normal,urgent'],
            'due_at'       => ['nullable', 'date'],
            'description'  => ['nullable', 'string', 'max:500'],
        ]);

        $now = now();
        $taskId = DB::table('tasks')->insertGetId([
            'created_by'   => $user->id,
            'unit_id'      => $validated['unit_id'] ?? null,
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'due_at'       => $validated['due_at'] ?? null,
            'priority'     => $validated['priority'],
            'status'       => 'pending',
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        DB::table('task_assignees')->insert([
            'task_id'    => $taskId,
            'user_id'    => $validated['assignee_id'],
            'status'     => 'pending',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()->route('operational.index', ['tab' => 'tasks'])
            ->with('success', 'Tugas operasional berhasil dikirimkan ke pelaksana.');
    }

    /**
     * Konfirmasi Selesai Tugas oleh Penerima (OP-02).
     */
    public function completeTask(Request $request, $id)
    {
        $user = Auth::user();
        $now = now();

        DB::table('task_assignees')
            ->where('task_id', $id)
            ->where('user_id', $user->id)
            ->update([
                'status'            => 'done',
                'done_at'           => $now,
                'confirmation_note' => $request->input('note', 'Telah diselesaikan.'),
                'updated_at'        => $now,
            ]);

        // Periksa jika seluruh assignee selesai -> task done
        $hasPending = DB::table('task_assignees')
            ->where('task_id', $id)
            ->where('status', '!=', 'done')
            ->exists();

        if (!$hasPending) {
            DB::table('tasks')->where('id', $id)->update([
                'status'       => 'done',
                'completed_at' => $now,
                'updated_at'   => $now,
            ]);
        }

        return redirect()->route('operational.index', ['tab' => 'tasks'])
            ->with('success', 'Tugas berhasil dikonfirmasi selesai.');
    }

    /**
     * Laporkan Kerusakan Sarpras (Tiket - OP-03).
     */
    public function storeTicket(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('ticket.report')) abort(403);

        $validated = $request->validate([
            'unit_id'       => ['required', 'exists:units,id'],
            'facility_id'   => ['nullable', 'exists:facilities,id'],
            'location_text' => ['required', 'string', 'max:150'],
            'type'          => ['required', 'in:damage,cleanliness,security,other'],
            'severity'      => ['required', 'in:low,medium,high'],
            'description'   => ['required', 'string', 'max:500'],
        ]);

        $now = now();
        $ticketNo = 'TCK' . date('ymd') . strtoupper(Str::random(4));

        DB::table('tickets')->insert([
            'ticket_no'     => $ticketNo,
            'reporter_id'   => $user->id,
            'unit_id'       => $validated['unit_id'],
            'facility_id'   => $validated['facility_id'] ?? null,
            'location_text' => $validated['location_text'],
            'type'          => $validated['type'],
            'description'   => $validated['description'],
            'severity'      => $validated['severity'],
            'status'        => 'open',
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        return redirect()->route('operational.index', ['tab' => 'tickets'])
            ->with('success', "Tiket kerusakan berhasil didaftarkan dengan Nomor: {$ticketNo}");
    }

    /**
     * Trigger Tombol Darurat (Panic Button - OP-04).
     */
    public function triggerPanic(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('panic.trigger')) abort(403);

        $user->load('units');
        $unitId = $user->units->first()->id ?? 2; // Default SD jika tidak terikat

        $now = now();
        $alertId = DB::table('panic_alerts')->insertGetId([
            'triggered_by'  => $user->id,
            'unit_id'       => $unitId,
            'location_text' => $request->input('location_text', 'Pos Gerbang Keamanan Kampus Deltamas'),
            'note'          => $request->input('note', 'Darurat: Mohon bantuan tim pimpinan dan sekuriti segera!'),
            'triggered_at'  => $now,
            'status'        => 'active',
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        // Notifikasi ke seluruh Koordinator, Kepsek, & TU (OP-04)
        $urgentRecipients = User::role(['koordinator', 'kepala_sekolah', 'tata_usaha'])->pluck('id');
        $recData = [];
        foreach ($urgentRecipients as $rid) {
            $recData[] = [
                'alert_id'    => $alertId,
                'user_id'     => $rid,
                'notified_at' => $now,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }
        if (!empty($recData)) {
            DB::table('panic_alert_recipients')->insert($recData);
        }

        return redirect()->route('operational.index', ['tab' => 'panic'])
            ->with('success', 'SINYAL PANIC BUTTON TELAH AKTIF! Notifikasi darurat dikirimkan ke pimpinan.');
    }

    /**
     * Catat Buku Tamu Digital oleh Satpam (OP-05).
     */
    public function storeGuest(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('guestbook.record')) abort(403);

        $validated = $request->validate([
            'unit_id'       => ['required', 'exists:units,id'],
            'guest_type'    => ['required', 'in:tamu,vendor,wali_murid,peminjam_eksternal'],
            'guest_name'    => ['required', 'string', 'max:150'],
            'institution'   => ['nullable', 'string', 'max:150'],
            'vehicle_plate' => ['nullable', 'string', 'max:15'],
            'purpose'       => ['required', 'string', 'max:255'],
            'visit_to'      => ['nullable', 'string', 'max:150'],
        ]);

        $now = now();
        DB::table('guest_logs')->insert([
            'unit_id'       => $validated['unit_id'],
            'recorded_by'   => $user->id,
            'guest_type'    => $validated['guest_type'],
            'guest_name'    => $validated['guest_name'],
            'institution'   => $validated['institution'] ?? null,
            'vehicle_plate' => strtoupper($validated['vehicle_plate'] ?? ''),
            'purpose'       => $validated['purpose'],
            'visit_to'      => $validated['visit_to'] ?? null,
            'check_in_at'   => $now,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        return redirect()->route('operational.index', ['tab' => 'guests'])
            ->with('success', 'Tamu berhasil dicatat ke Buku Tamu Digital.');
    }
}
