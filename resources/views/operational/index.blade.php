@extends('layouts.app')

@section('title', 'Operasional Lapangan & Keamanan — SIAP PL Deltamas')

@section('content')
<div class="w-full px-4 py-6 sm:px-6 sm:py-8 lg:px-8 space-y-7">

    <!-- Header Section: Clean Responsive Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h2 class="font-display text-2xl sm:text-3xl font-semibold text-slate-900 dark:text-white tracking-tight">
                Operasional Lapangan & Keamanan
            </h2>
            <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                Pusat penugasan langsung, helpdesk kerusakan, tombol darurat, dan buku tamu gerbang.
            </p>
        </div>

        <!-- Panic Button Action (OP-04) -->
        @if($user->can('panic.trigger'))
            <form action="{{ route('operational.panic') }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin memicu sinyal PANIC BUTTON darurat? Notifikasi prioritas tinggi akan segera dikirimkan ke seluruh pimpinan kampus.')">
                @csrf
                <button type="submit" class="w-full sm:w-auto py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 active:scale-95 text-white font-extrabold text-xs shadow-lg shadow-rose-600/30 transition flex items-center justify-center gap-2 animate-pulse">
                    <i class="bi bi-exclamation-octagon-fill text-sm"></i>
                    <span>PANIC BUTTON (DARURAT)</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Mobile-Optimized Sub Navigation (Scrollable Pill Tabs) -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3 dark:border-slate-800">
        <a href="{{ route('operational.index', ['tab' => 'tasks']) }}" 
           class="px-3.5 py-2 text-xs font-bold transition flex items-center gap-2 rounded-xl shrink-0 whitespace-nowrap {{ $viewTab === 'tasks' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
            <i class="bi bi-check2-square"></i>
            <span>Tugas Langsung ({{ $tasks->count() }})</span>
        </a>
        <a href="{{ route('operational.index', ['tab' => 'tickets']) }}" 
           class="px-3.5 py-2 text-xs font-bold transition flex items-center gap-2 rounded-xl shrink-0 whitespace-nowrap {{ $viewTab === 'tickets' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
            <i class="bi bi-wrench-adjustable"></i>
            <span>Tiket Kerusakan ({{ $tickets->count() }})</span>
        </a>
        <a href="{{ route('operational.index', ['tab' => 'panic']) }}" 
           class="px-3.5 py-2 text-xs font-bold transition flex items-center gap-2 rounded-xl shrink-0 whitespace-nowrap {{ $viewTab === 'panic' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
            <i class="bi bi-bell-fill"></i>
            <span>Log Panic ({{ $panicAlerts->count() }})</span>
        </a>
        <a href="{{ route('operational.index', ['tab' => 'guests']) }}" 
           class="px-3.5 py-2 text-xs font-bold transition flex items-center gap-2 rounded-xl shrink-0 whitespace-nowrap {{ $viewTab === 'guests' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
            <i class="bi bi-journal-text"></i>
            <span>Buku Tamu ({{ $guestLogs->count() }})</span>
        </a>
    </div>

    @if($viewTab === 'tasks')
        <!-- TAB 1: DIRECT TASKING (OP-01 & OP-02) -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">Instruksi kerja pimpinan langsung ke TU, Pelaksana Kebersihan, dan Satpam.</span>
                @if($user->can('task.assign'))
                    <button type="button" onclick="document.getElementById('modalTask').classList.remove('hidden')" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="bi bi-plus-lg"></i>
                        <span>Beri Tugas Baru</span>
                    </button>
                @endif
            </div>

            <!-- Task List Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($tasks as $t)
                    <div class="rounded-2xl border {{ $t->status === 'done' ? 'border-emerald-200 dark:border-emerald-500/30 bg-emerald-50/20 dark:bg-emerald-950/10' : ($t->priority === 'urgent' ? 'border-rose-300 dark:border-rose-500/40 bg-rose-50/30 dark:bg-rose-950/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800') }} p-5 space-y-3 shadow-sm transition-colors">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $t->priority === 'urgent' ? 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                                Prioritas: {{ $t->priority }}
                            </span>
                            @if($t->status === 'done')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300">
                                    Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-300">
                                    Menunggu
                                </span>
                            @endif
                        </div>

                        <div>
                            <h4 class="text-base font-bold text-slate-900 dark:text-white">{{ $t->title }}</h4>
                            @if($t->description)
                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">{{ $t->description }}</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-600 dark:text-slate-300">
                            <div>Dari: <strong class="text-slate-700 dark:text-slate-300">{{ $t->creator_name }}</strong></div>
                            @if($t->due_at)
                                <div class="font-mono">Tenggat: {{ \Carbon\Carbon::parse($t->due_at)->format('d M H:i') }} WIB</div>
                            @endif
                        </div>

                        @if($t->status !== 'done' && $user->can('task.receive'))
                            <form action="{{ route('operational.complete_task', $t->id) }}" method="POST" class="pt-1">
                                @csrf
                                <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow">
                                    <i class="bi bi-check2-circle text-base"></i>
                                    <span>Konfirmasi Selesai Tugas (Satu Ketukan)</span>
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 py-12 text-center text-slate-600 dark:text-slate-400 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-white/40 dark:bg-slate-800/40">
                        <i class="bi bi-check2-all text-3xl"></i>
                        <p class="mt-2 text-xs font-semibold">Tidak ada tugas lapangan aktif saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    @elseif($viewTab === 'tickets')
        <!-- TAB 2: TIKET KERUSAKAN (OP-03) -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">Helpdesk pelaporan kerusakan sarpras dan insiden lapangan.</span>
                <button type="button" onclick="document.getElementById('modalTicket').classList.remove('hidden')" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="bi bi-plus-lg"></i>
                    <span>Lapor Kerusakan</span>
                </button>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 shadow-sm transition-colors">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700 font-semibold">
                        <tr>
                            <th class="py-3 px-4">No Tiket</th>
                            <th class="py-3 px-4">Lokasi & Fasilitas</th>
                            <th class="py-3 px-4">Uraian Kerusakan</th>
                            <th class="py-3 px-4">Pelapor</th>
                            <th class="py-3 px-4">Status & Tingkat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($tickets as $tk)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                                <td class="py-3 px-4 font-mono font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">{{ $tk->ticket_no }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $tk->location_text }}</div>
                                    <div class="text-[11px] text-slate-600 dark:text-slate-300">{{ $tk->facility_name ?? "Unit {$tk->unit_code}" }}</div>
                                </td>
                                <td class="py-3 px-4 max-w-xs">{{ $tk->description }}</td>
                                <td class="py-3 px-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $tk->reporter_name }}</td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $tk->severity === 'high' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                        {{ $tk->severity }}
                                    </span>
                                    <span class="ml-1 px-2 py-0.5 rounded text-[10px] uppercase font-semibold bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                        {{ $tk->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-600 dark:text-slate-400">Tidak ada tiket kerusakan yang dilaporkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($viewTab === 'panic')
        <!-- TAB 3: PANIC BUTTON LOG (OP-04) -->
        <div class="space-y-4">
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/20 border border-rose-300 dark:border-rose-500/40 text-xs text-rose-800 dark:text-rose-300 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="bi bi-shield-exclamation text-xl text-rose-600"></i>
                    <span>Log aktivasi Panic Button dari pos keamanan dan pelaksana darurat.</span>
                </div>
                <span class="font-mono font-bold uppercase text-[10px] bg-rose-200 dark:bg-rose-900/50 px-2 py-1 rounded">Prioritas Darurat</span>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 shadow-sm transition-colors">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700 font-semibold">
                        <tr>
                            <th class="py-3 px-4">Waktu Pemicu</th>
                            <th class="py-3 px-4">Petugas Pemicu</th>
                            <th class="py-3 px-4">Lokasi Darurat</th>
                            <th class="py-3 px-4">Pesan / Kondisi</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($panicAlerts as $pa)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition {{ $pa->status === 'active' ? 'bg-rose-50/50 dark:bg-rose-950/30' : '' }}">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($pa->triggered_at)->translatedFormat('d M Y, H:i:s') }} WIB
                                </td>
                                <td class="py-3 px-4 font-bold text-rose-600 dark:text-rose-400 whitespace-nowrap">{{ $pa->trigger_name }}</td>
                                <td class="py-3 px-4 whitespace-nowrap">{{ $pa->location_text }}</td>
                                <td class="py-3 px-4 text-slate-800 dark:text-slate-200">{{ $pa->note }}</td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($pa->status === 'active')
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-rose-600 text-white animate-pulse">
                                            AKTIF
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                            Ditangani
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-600 dark:text-slate-400">Belum pernah ada sinyal darurat yang diaktifkan. Kondisi kampus aman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- TAB 4: BUKU TAMU DIGITAL (OP-05) -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">Pencatatan identitas tamu, vendor, dan wali murid di gerbang masuk kampus.</span>
                @if($user->can('guestbook.record'))
                    <button type="button" onclick="document.getElementById('modalGuest').classList.remove('hidden')" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="bi bi-person-plus-fill"></i>
                        <span>Catat Tamu Masuk</span>
                    </button>
                @endif
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 shadow-sm transition-colors">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700 font-semibold">
                        <tr>
                            <th class="py-3 px-4">Jam Masuk</th>
                            <th class="py-3 px-4">Nama Tamu & Instansi</th>
                            <th class="py-3 px-4">Plat Kendaraan</th>
                            <th class="py-3 px-4">Tujuan / Menuju</th>
                            <th class="py-3 px-4">Satpam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($guestLogs as $gl)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($gl->check_in_at)->translatedFormat('d M H:i') }} WIB
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $gl->guest_name }}</div>
                                    <div class="text-[11px] text-slate-600 dark:text-slate-300">{{ $gl->institution ?? 'Pribadi' }} ({{ ucfirst($gl->guest_type) }})</div>
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">{{ $gl->vehicle_plate ?: '-' }}</td>
                                <td class="py-3 px-4">
                                    <div class="text-slate-800 dark:text-slate-200">{{ $gl->purpose }}</div>
                                    @if($gl->visit_to)
                                        <div class="text-[11px] text-slate-600 dark:text-slate-400 font-mono">Menuju: {{ $gl->visit_to }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $gl->satpam_name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-600 dark:text-slate-400">Belum ada catatan buku tamu untuk hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

<!-- Modal 1: Form Direct Tasking -->
<div id="modalTask" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-lg space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">Beri Tugas Lapangan (Direct Tasking)</h3>
            <button type="button" onclick="document.getElementById('modalTask').classList.add('hidden')" class="text-slate-600 dark:text-slate-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="{{ route('operational.store_task') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Tugas</label>
                <input type="text" name="title" required placeholder="Contoh: Bersihkan Aula SMA & pasang sound system" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pelaksana Ditugaskan</label>
                    <select name="assignee_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        @foreach(\App\Models\User::role(['satpam_pelaksana', 'tata_usaha'])->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->job_title }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Prioritas</label>
                    <select name="priority" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent (Penting)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Rincian Instruksi</label>
                <textarea name="description" rows="3" placeholder="Rincian acuan tugas..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white"></textarea>
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalTask').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold">Kirim Tugas</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Form Lapor Tiket Kerusakan -->
<div id="modalTicket" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-lg space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">Lapor Kerusakan Sarpras / Insiden</h3>
            <button type="button" onclick="document.getElementById('modalTicket').classList.add('hidden')" class="text-slate-600 dark:text-slate-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="{{ route('operational.store_ticket') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Unit Lokasi</label>
                    <select name="unit_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        @foreach(\App\Models\Unit::all() as $un)
                            <option value="{{ $un->id }}">Unit {{ $un->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tingkat Keparahan</label>
                    <select name="severity" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        <option value="low">Rendah (Low)</option>
                        <option value="medium" selected>Sedang (Medium)</option>
                        <option value="high">Tinggi (High / Rusak Berat)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Lokasi Ruang / Sarana</label>
                <input type="text" name="location_text" required placeholder="Contoh: Lab Komputer SMP, AC Bocor" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Kerusakan</label>
                <select name="type" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                    <option value="damage">Kerusakan Fasilitas / Fisik</option>
                    <option value="cleanliness">Kebersihan</option>
                    <option value="security">Keamanan / Insiden</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Uraian Kerusakan</label>
                <textarea name="description" rows="3" required placeholder="Jelaskan kondisi kerusakan..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white"></textarea>
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalTicket').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold">Kirim Laporan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Form Catat Tamu Masuk -->
<div id="modalGuest" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-lg space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">Catat Tamu Masuk Gerbang (Buku Tamu)</h3>
            <button type="button" onclick="document.getElementById('modalGuest').classList.add('hidden')" class="text-slate-600 dark:text-slate-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="{{ route('operational.store_guest') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Tamu</label>
                    <input type="text" name="guest_name" required placeholder="Nama lengkap..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Tamu</label>
                    <select name="guest_type" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        <option value="tamu">Tamu Umum / Dinas</option>
                        <option value="wali_murid">Orang Tua / Wali Murid</option>
                        <option value="vendor">Vendor / Ekspedisi</option>
                        <option value="peminjam_eksternal">Peminjam Fasilitas</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Instansi / Asal</label>
                    <input type="text" name="institution" placeholder="Contoh: PT ABC, Paroki..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Plat Nomor Kendaraan</label>
                    <input type="text" name="vehicle_plate" placeholder="B 1234 ABC" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Unit Tujuan</label>
                    <select name="unit_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        @foreach(\App\Models\Unit::all() as $un)
                            <option value="{{ $un->id }}">Unit {{ $un->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Bertemu / Menuju Ke</label>
                    <input type="text" name="visit_to" placeholder="Kepsek SD / Ruang TU" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Keperluan / Tujuan Kunjungan</label>
                <textarea name="purpose" rows="2" required placeholder="Alasan kunjungan..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white"></textarea>
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalGuest').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold">Simpan Buku Tamu</button>
            </div>
        </form>
    </div>
</div>
@endsection
