@extends('layouts.app')

@section('title', 'Beranda — SIAP PL Deltamas')

@section('content')
<div class="w-full space-y-8 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

    <section class="grid items-end gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(16rem,0.8fr)]">
        <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gold-600 dark:text-gold-400">Beranda pegawai</p>
            <h2 class="font-display text-3xl leading-tight text-slate-900 dark:text-white sm:text-4xl">
                Selamat datang, {{ $user->name }}
            </h2>
            <p class="max-w-2xl text-sm text-slate-600 dark:text-slate-300">
                {{ $user->job_title ?? 'Pegawai Kampus Pangudi Luhur Deltamas' }}
            </p>
            <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Login Berhasil sebagai {{ strtoupper(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'STAF')) }}
                </span>
            </div>
        </div>

        <dl class="grid grid-cols-1 divide-y divide-slate-200 overflow-hidden rounded-2xl border border-slate-200 bg-white text-sm dark:divide-slate-800 dark:border-slate-800 dark:bg-slate-900 sm:grid-cols-3 sm:divide-x sm:divide-y-0 lg:grid-cols-1 lg:divide-x-0 lg:divide-y">
            <div class="px-4 py-3">
                <dt class="text-[11px] uppercase tracking-wide text-slate-500">NIP</dt>
                <dd class="mt-0.5 font-mono font-semibold text-slate-900 dark:text-white">{{ $user->nip ?? '-' }}</dd>
            </div>
            <div class="px-4 py-3">
                <dt class="text-[11px] uppercase tracking-wide text-slate-500">Tahun Ajaran</dt>
                <dd class="mt-0.5 font-semibold text-slate-900 dark:text-white">2026/2027 (Ganjil)</dd>
            </div>
            <div class="px-4 py-3">
                <dt class="text-[11px] uppercase tracking-wide text-slate-500">Waktu</dt>
                <dd class="mt-0.5 font-mono text-slate-700 dark:text-slate-200">{{ now()->format('d M Y, H:i') }} WIB</dd>
            </div>
        </dl>
    </section>

    <section class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between sm:p-5">
        <div class="flex items-start gap-3">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-ink-900 text-lg text-gold-300">
                <i class="bi bi-bell"></i>
            </span>
            <div>
                <h3 class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                    <span>Notifikasi Smartphone (Web Push PWA)</span>
                    <span class="rounded-full bg-gold-500/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-gold-600 dark:text-gold-300">Baru</span>
                </h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                    Dapatkan siaran kampus, persetujuan izin, dan alarm darurat langsung di status bar HP.
                </p>
            </div>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <button type="button" onclick="openNotificationModal()" class="inline-flex items-center gap-1.5 rounded-xl bg-ink-900 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-ink-800">
                <i class="bi bi-bell"></i>
                <span>Atur Notifikasi</span>
            </button>
            <button type="button" onclick="handleSendTestPush()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                <i class="bi bi-send-check"></i>
                <span>Tes Notifikasi</span>
            </button>
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h3 class="font-display text-2xl text-slate-900 dark:text-white">Akses Menu Cepat</h3>
                <p class="mt-1 text-xs text-slate-500">Atau buka menu navigasi.</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $shortcuts = [
                    ['route' => 'announcements.index', 'icon' => 'bi-megaphone', 'title' => 'Papan Siaran', 'hint' => 'Broadcast Info'],
                    ['route' => 'agendas.index', 'icon' => 'bi-calendar3', 'title' => 'Agenda Kampus', 'hint' => 'Jadwal Kegiatan'],
                    ['route' => 'leaves.index', 'icon' => 'bi-file-earmark-check', 'title' => 'Perizinan Staf', 'hint' => 'Izin & Guru Inval'],
                    ['route' => 'facilities.index', 'icon' => 'bi-building', 'title' => 'Fasilitas Kampus', 'hint' => 'Reservasi & QR'],
                    ['route' => 'operational.index', 'icon' => 'bi-tools', 'title' => 'Operasional', 'hint' => 'Tugas, Tiket, Tamu'],
                    ['route' => 'letters.index', 'icon' => 'bi-envelope-paper', 'title' => 'Tata Persuratan', 'hint' => 'Arsip & No. Surat'],
                    ['route' => 'messages.index', 'icon' => 'bi-chat-left-text', 'title' => 'Pesan', 'hint' => 'Satu Arah'],
                ];
            @endphp
            @foreach($shortcuts as $item)
                <a href="{{ route($item['route']) }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-blue-700 hover:shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-400">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-slate-100 text-lg text-blue-800 transition group-hover:bg-ink-900 group-hover:text-gold-300 dark:bg-slate-800 dark:text-blue-300">
                        <i class="bi {{ $item['icon'] }}"></i>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-slate-900 dark:text-white">{{ $item['title'] }}</span>
                        <span class="block text-xs text-slate-500">{{ $item['hint'] }}</span>
                    </span>
                </a>
            @endforeach
            <button type="button" onclick="showDevelopmentNotice()" class="group flex items-center gap-3 rounded-2xl border border-dashed border-slate-300 bg-white/70 p-4 text-left transition hover:border-blue-700 dark:border-slate-700 dark:bg-slate-900/70">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-slate-100 text-lg text-blue-800 dark:bg-slate-800 dark:text-blue-300">
                    <i class="bi bi-book-half"></i>
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-slate-900 dark:text-white">Kurikulum</span>
                    <span class="block text-xs text-slate-500">Akademik & Nilai</span>
                </span>
            </button>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Informasi Akun Pegawai</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 dark:border-slate-800">
                    <dt class="text-slate-500">Peran Utama</dt>
                    <dd class="text-right font-semibold text-slate-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'Staf')) }}</dd>
                </div>
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 dark:border-slate-800">
                    <dt class="text-slate-500">Kategori</dt>
                    <dd class="text-right font-medium text-slate-900 dark:text-white">{{ ucfirst($user->staff_category ?? 'Umum') }}</dd>
                </div>
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 dark:border-slate-800">
                    <dt class="text-slate-500">Penempatan</dt>
                    <dd class="text-right font-medium text-slate-900 dark:text-white">
                        {{ $user->units->isNotEmpty() ? $user->units->pluck('name')->implode(', ') : 'Kampus Deltamas' }}
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Status Akun</dt>
                    <dd class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">Aktif</dd>
                </div>
            </dl>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Hak Akses Otorisasi (RBAC)</h3>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                Akun ini memiliki <strong class="font-mono text-slate-900 dark:text-white">{{ $user->getAllPermissions()->count() }} izin aktif</strong> yang diatur melalui paket <code class="font-mono text-blue-700 dark:text-blue-300">spatie/laravel-permission</code>.
            </p>
            <div class="mt-4 flex max-h-40 flex-wrap gap-1.5 overflow-y-auto">
                @foreach($user->getAllPermissions() as $p)
                    <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 font-mono text-[11px] text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">
                        <i class="bi bi-check2 text-emerald-600"></i>
                        {{ $p->name }}
                    </span>
                @endforeach
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Status Modul Sistem</h3>
            <ul class="mt-4 space-y-2 text-sm">
                <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-3 dark:bg-slate-950">
                    <span class="font-medium text-slate-800 dark:text-slate-200">Komunikasi & Fasilitas</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                </li>
                <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-3 dark:bg-slate-950">
                    <span class="font-medium text-slate-800 dark:text-slate-200">Perizinan & Operasional</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                </li>
                <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-3 dark:bg-slate-950">
                    <span class="font-medium text-slate-800 dark:text-slate-200">Modul Kurikulum</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600"><i class="bi bi-info-circle-fill"></i> Pengembangan</span>
                </li>
            </ul>
        </article>
    </section>
</div>
@endsection
