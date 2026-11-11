@extends('layouts.app')

@section('title', 'Panel Koordinator — SIAP PL Deltamas')

@section('content')
<div class="w-full space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

    {{-- Header + navigasi panel --}}
    <div>
        <p class="label-caps">Pusat Kendali</p>
        <h2 class="mt-1 font-display text-2xl text-slate-900 dark:text-white sm:text-3xl">Panel Koordinator Sekolah</h2>
        <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
            Visibilitas penuh lintas unit: akun pengguna, delegasi wewenang, jejak audit, dan data induk kampus.
        </p>
    </div>

    @include('admin.partials.nav', ['active' => 'dashboard'])

    @if(session('success'))
        <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
            <i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Antrean keputusan --}}
    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @php
            $queueCards = [
                ['label' => 'Izin Menunggu Validasi', 'value' => $queue['leaves'], 'icon' => 'bi-file-earmark-check', 'route' => 'leaves.index'],
                ['label' => 'Reservasi Perlu Otorisasi', 'value' => $queue['bookings'], 'icon' => 'bi-building-check', 'route' => 'facilities.index'],
                ['label' => 'Grup Menunggu Persetujuan', 'value' => $queue['groups'], 'icon' => 'bi-people', 'route' => 'groups.index'],
                ['label' => 'Tiket Kerusakan Terbuka', 'value' => $queue['tickets'], 'icon' => 'bi-wrench-adjustable', 'route' => 'operational.index'],
            ];
        @endphp
        @foreach($queueCards as $c)
            <a href="{{ route($c['route']) }}" class="portal-card group p-4 transition hover:border-gold-400 hover:shadow-card-hover">
                <div class="flex items-start justify-between gap-2">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-ink-900 text-base text-gold-300">
                        <i class="bi {{ $c['icon'] }}"></i>
                    </span>
                    @if($c['value'] > 0)
                        <span class="rounded-full bg-rose-700 px-1.5 py-0.5 text-[9px] font-bold uppercase text-white dark:bg-rose-600">Perlu Aksi</span>
                    @endif
                </div>
                <p class="mt-3 font-display text-3xl leading-none text-slate-900 dark:text-white">{{ $c['value'] }}</p>
                <p class="mt-1.5 text-[11px] font-medium leading-snug text-slate-600 dark:text-slate-300">{{ $c['label'] }}</p>
            </a>
        @endforeach
    </section>

    {{-- Statistik akun & operasional --}}
    <section class="grid gap-4 lg:grid-cols-2">
        <article class="portal-card p-5">
            <h3 class="label-caps">Rekapitulasi Akun Pengguna</h3>
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                @php
                    $uStats = [
                        ['Total Akun', $userStats['total'], 'text-slate-900 dark:text-white'],
                        ['Aktif', $userStats['active'], 'text-emerald-700 dark:text-emerald-300'],
                        ['Dinonaktifkan', $userStats['suspended'], 'text-rose-700 dark:text-rose-300'],
                        ['Pengguna Umum', $userStats['external'], 'text-blue-800 dark:text-blue-300'],
                        ['Belum Pernah Login', $userStats['never_login'], 'text-gold-700 dark:text-gold-300'],
                    ];
                @endphp
                @foreach($uStats as [$label, $val, $cls])
                    <div class="rounded-xl bg-slate-50 px-3 py-3 dark:bg-white/5">
                        <p class="font-display text-2xl leading-none {{ $cls }}">{{ $val }}</p>
                        <p class="mt-1 text-[10px] font-medium leading-snug text-slate-600 dark:text-slate-400">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="portal-card p-5">
            <h3 class="label-caps">Operasional Bulan Berjalan</h3>
            <dl class="mt-4 space-y-2 text-sm">
                @php
                    $ops = [
                        ['Tugas Lapangan Belum Selesai', $operational['tasks_open'], 'bi-check2-square'],
                        ['Sinyal Darurat Bulan Ini', $operational['panic_month'], 'bi-bell'],
                        ['Tamu Masuk Hari Ini', $operational['guests_today'], 'bi-person-badge'],
                        ['Surat Teregistrasi Bulan Ini', $operational['letters_month'], 'bi-envelope-paper'],
                    ];
                @endphp
                @foreach($ops as [$label, $val, $icon])
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-white/5">
                        <dt class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-200">
                            <i class="bi {{ $icon }} text-gold-600 dark:text-gold-400"></i>
                            <span>{{ $label }}</span>
                        </dt>
                        <dd class="shrink-0 font-mono text-base font-semibold text-slate-900 dark:text-white">{{ $val }}</dd>
                    </div>
                @endforeach
            </dl>
        </article>
    </section>

    {{-- Monitor kehadiran terpadu --}}
    <section class="portal-card overflow-hidden">
        <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
            <div>
                <h3 class="font-display text-base text-slate-900 dark:text-white">Ketidakhadiran Hari Ini</h3>
                <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">Rekapitulasi lintas unit secara langsung &mdash; {{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <span class="shrink-0 rounded-full border border-slate-200 bg-white px-2.5 py-1 font-mono text-xs font-semibold text-slate-800 dark:border-white/15 dark:bg-white/5 dark:text-slate-200">
                {{ $absentToday->count() }} orang
            </span>
        </header>

        <div class="divide-y divide-slate-100 dark:divide-white/10 sm:hidden">
            @forelse($absentToday as $a)
                <div class="flex items-start justify-between gap-3 p-4">
                    <div class="min-w-0">
                        <p class="break-words text-sm font-semibold text-slate-900 dark:text-white">{{ $a->name }}</p>
                        <p class="mt-0.5 font-mono text-[11px] text-slate-600 dark:text-slate-400">{{ $a->nip }} &middot; Unit {{ $a->unit_code ?? '—' }}</p>
                    </div>
                    <span class="shrink-0 whitespace-nowrap rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-800 dark:bg-amber-500/15 dark:text-amber-300">{{ $a->leave_type }}</span>
                </div>
            @empty
                <p class="p-8 text-center text-xs text-slate-600 dark:text-slate-400">Seluruh pegawai hadir hari ini.</p>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto sm:block">
            <table class="w-full border-collapse text-left text-xs">
                <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-300">
                    <tr>
                        <th class="px-5 py-3">Nama Pegawai</th>
                        <th class="px-5 py-3">NIP</th>
                        <th class="px-5 py-3">Unit</th>
                        <th class="px-5 py-3">Jenis</th>
                        <th class="px-5 py-3">Periode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 dark:divide-white/10 dark:text-slate-300">
                    @forelse($absentToday as $a)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-white/5">
                            <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $a->name }}</td>
                            <td class="px-5 py-3 font-mono">{{ $a->nip }}</td>
                            <td class="px-5 py-3 font-mono">{{ $a->unit_code ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="whitespace-nowrap rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-800 dark:bg-amber-500/15 dark:text-amber-300">{{ $a->leave_type }}</span>
                            </td>
                            <td class="px-5 py-3 font-mono text-[11px]">
                                {{ \Carbon\Carbon::parse($a->start_date)->format('d/m') }} &ndash; {{ \Carbon\Carbon::parse($a->end_date)->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <i class="bi bi-emoji-smile text-2xl text-emerald-600"></i>
                                <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Seluruh pegawai hadir hari ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Tren data: perizinan per unit & intensitas fasilitas --}}
    <section class="grid gap-4 lg:grid-cols-2">
        <article class="portal-card p-5">
            <h3 class="label-caps">Perizinan Bulan Ini per Unit</h3>
            @php $maxLeave = max(1, $leaveByUnit->max('total') ?? 1); @endphp
            <div class="mt-4 space-y-3">
                @forelse($leaveByUnit as $l)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-xs">
                            <span class="font-semibold text-slate-800 dark:text-slate-200">Unit {{ $l->unit_code ?? 'Kampus' }}</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300">{{ $l->total }} pengajuan</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-white/10">
                            <div class="h-full rounded-full bg-blue-700 dark:bg-blue-500" style="width: {{ round($l->total / $maxLeave * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-xs text-slate-600 dark:text-slate-400">Belum ada pengajuan izin bulan ini.</p>
                @endforelse
            </div>
        </article>

        <article class="portal-card p-5">
            <h3 class="label-caps">Intensitas Peminjaman Fasilitas</h3>
            @php $maxFac = max(1, $facilityUsage->max('total') ?? 1); @endphp
            <div class="mt-4 space-y-3">
                @forelse($facilityUsage as $f)
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3 text-xs">
                            <span class="min-w-0 flex-1 truncate font-semibold text-slate-800 dark:text-slate-200">{{ $f->name }}</span>
                            <span class="shrink-0 font-mono text-slate-700 dark:text-slate-300">{{ $f->total }}x</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-white/10">
                            <div class="h-full rounded-full bg-gold-500" style="width: {{ round($f->total / $maxFac * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-xs text-slate-600 dark:text-slate-400">Belum ada data peminjaman fasilitas.</p>
                @endforelse
            </div>
        </article>
    </section>

    {{-- Engagement: akun pasif & login terakhir --}}
    <section class="grid gap-4 lg:grid-cols-2">
        <article class="portal-card overflow-hidden">
            <header class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                <h3 class="font-display text-base text-slate-900 dark:text-white">Akun Pasif</h3>
                <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">Belum pernah login atau tidak aktif lebih dari 30 hari.</p>
            </header>
            <ul class="feed-divide">
                @forelse($passiveUsers as $p)
                    <li class="flex items-start justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $p->name }}</p>
                            <p class="mt-0.5 font-mono text-[11px] text-slate-600 dark:text-slate-400">{{ $p->nip }} &middot; {{ $p->unit_codes ?? 'Kampus' }}</p>
                        </div>
                        <span class="shrink-0 whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $p->last_login_at ? 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-500/15 dark:text-rose-300' }}">
                            {{ $p->last_login_at ? \Carbon\Carbon::parse($p->last_login_at)->diffForHumans(null, true).' lalu' : 'Belum pernah' }}
                        </span>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-xs text-slate-600 dark:text-slate-400">Seluruh akun aktif digunakan.</li>
                @endforelse
            </ul>
        </article>

        <article class="portal-card overflow-hidden">
            <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
                <div>
                    <h3 class="font-display text-base text-slate-900 dark:text-white">Riwayat Login Terakhir</h3>
                    <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">Pemantauan akses sistem.</p>
                </div>
                <a href="{{ route('admin.audit', ['tab' => 'logins']) }}" class="inline-flex shrink-0 items-center gap-1 text-xs font-medium text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">
                    Semua <i class="bi bi-chevron-right text-[10px]"></i>
                </a>
            </header>
            <ul class="feed-divide">
                @forelse($recentLogins as $r)
                    <li class="flex items-start justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $r->name ?? $r->identifier }}</p>
                            <p class="mt-0.5 font-mono text-[11px] text-slate-600 dark:text-slate-400">
                                {{ $r->nip ?? $r->identifier }} &middot; {{ $r->ip_address }}
                            </p>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="block whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $r->success ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-500/15 dark:text-rose-300' }}">
                                {{ $r->success ? 'Berhasil' : 'Gagal' }}
                            </span>
                            <span class="mt-1 block font-mono text-[10px] text-slate-600 dark:text-slate-400">{{ \Carbon\Carbon::parse($r->logged_in_at)->format('d/m H:i') }}</span>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-xs text-slate-600 dark:text-slate-400">Belum ada riwayat login tercatat.</li>
                @endforelse
            </ul>
        </article>
    </section>
</div>
@endsection
