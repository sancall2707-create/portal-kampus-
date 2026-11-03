@extends('layouts.app')

@section('title', 'Perizinan Pegawai — SIAP PL Deltamas')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span>Perizinan & Ketidakhadiran Pegawai</span>
                <span class="text-xs font-mono font-normal px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                    {{ $leaveRequests->count() }} Pengajuan
                </span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Alur pengajuan izin, sakit, cuti mandiri, penunjukan guru inval, dan persetujuan berjenjang.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            @if($user->can('leave.request'))
                <a href="{{ route('leaves.create') }}" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                    <i class="bi bi-file-earmark-plus-fill"></i>
                    <span>Ajukan Izin / Sakit</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Monitor Ketidakhadiran Real-Time Hari Ini (LV-07) -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-5 space-y-3 shadow-sm dark:shadow-xl transition-colors">
        <div class="flex items-center justify-between">
            <h3 class="text-xs uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                <i class="bi bi-person-x-fill text-rose-500 text-sm"></i>
                <span>Ketidakhadiran Resmi Hari Ini ({{ now()->translatedFormat('d F Y') }})</span>
            </h3>
            <span class="text-[11px] font-mono font-bold text-slate-700 dark:text-slate-300">{{ $absentToday->count() }} Pegawai Tidak Hadir</span>
        </div>

        @if($absentToday->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach($absentToday as $ab)
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $ab->name }}</span>
                            <span class="px-2 py-0.2 rounded text-[10px] uppercase font-bold bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                                {{ $ab->leave_name }} ({{ $ab->unit_code }})
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Alasan: {{ $ab->reason }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-xs text-slate-500 dark:text-slate-400 italic">
                Seluruh pegawai dijadwalkan hadir normal hari ini (tidak ada izin yang aktif pada tanggal ini).
            </div>
        @endif
    </div>

    <!-- Filter Pills (LV-06) -->
    <div class="flex items-center space-x-2 text-xs overflow-x-auto pb-1">
        <a href="{{ route('leaves.index', ['status' => 'all']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $statusFilter === 'all' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Semua Pengajuan
        </a>
        <a href="{{ route('leaves.index', ['status' => 'pending_unit']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $statusFilter === 'pending_unit' ? 'bg-amber-600 border-amber-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Menunggu Kepala Sekolah
        </a>
        <a href="{{ route('leaves.index', ['status' => 'pending_coordinator']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $statusFilter === 'pending_coordinator' ? 'bg-purple-600 border-purple-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Menunggu Koordinator
        </a>
        <a href="{{ route('leaves.index', ['status' => 'approved']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $statusFilter === 'approved' ? 'bg-emerald-600 border-emerald-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Disetujui Resmi
        </a>
        <a href="{{ route('leaves.index', ['status' => 'rejected']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $statusFilter === 'rejected' ? 'bg-rose-600 border-rose-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Ditolak
        </a>
    </div>

    <!-- Table of Leave Requests (LV-06) -->
    <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/40 shadow-sm dark:shadow-xl backdrop-blur transition-colors">
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700 font-semibold">
                <tr>
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Pegawai Pemohon</th>
                    <th class="py-3 px-4">Jenis Izin</th>
                    <th class="py-3 px-4">Rentang Waktu</th>
                    <th class="py-3 px-4">Guru Inval (Pengganti)</th>
                    <th class="py-3 px-4">Status Persetujuan</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                @forelse($leaveRequests as $idx => $lr)
                    @php
                        $sub = $substitutes[$lr->id] ?? null;
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                        <td class="py-3 px-4 font-mono text-slate-400">{{ $idx + 1 }}</td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $lr->applicant_name }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $lr->applicant_nip ?? '-' }} · Unit {{ $lr->unit_code }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $lr->leave_type_name }}</span>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $lr->total_days }} Hari Kerja</div>
                        </td>
                        <td class="py-3 px-4 font-mono">
                            <div class="text-slate-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($lr->start_date)->translatedFormat('d M Y') }}</div>
                            <div class="text-[11px] text-slate-400">s.d. {{ \Carbon\Carbon::parse($lr->end_date)->translatedFormat('d M Y') }}</div>
                        </td>
                        <td class="py-3 px-4">
                            @if($sub)
                                <div class="font-medium text-slate-900 dark:text-white">{{ $sub->substitute_name }}</div>
                                @if($sub->status === 'acknowledged')
                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono flex items-center gap-1">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>Dikonfirmasi</span>
                                    </span>
                                @else
                                    <span class="text-[10px] text-amber-600 dark:text-amber-400 font-mono flex items-center gap-1">
                                        <i class="bi bi-clock"></i>
                                        <span>Menunggu Konfirmasi</span>
                                    </span>
                                @endif
                            @else
                                <span class="text-slate-400 italic text-[11px]">- Tanpa Inval -</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($lr->status === 'approved')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                                    <i class="bi bi-check2-circle"></i>
                                    <span>Diizinkan</span>
                                </span>
                            @elseif($lr->status === 'pending_unit')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                    <i class="bi bi-hourglass-split"></i>
                                    <span>Menunggu Kepsek</span>
                                </span>
                            @elseif($lr->status === 'pending_coordinator')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">
                                    <i class="bi bi-hourglass-split"></i>
                                    <span>Menunggu Koordinator</span>
                                </span>
                            @elseif($lr->status === 'rejected')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                                    <i class="bi bi-x-circle"></i>
                                    <span>Ditolak</span>
                                </span>
                            @else
                                <span class="text-[10px] uppercase font-bold text-slate-400 font-mono">{{ $lr->status }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('leaves.show', $lr->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-300 dark:border-slate-700 font-semibold text-[11px] transition inline-flex items-center gap-1">
                                <span>Detail</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="bi bi-inbox text-3xl"></i>
                            <div class="mt-2 text-xs font-semibold">Tidak ada data perizinan pada kategori ini.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
