@extends('layouts.app')

@section('title', 'Dasbor — SIAP PL Deltamas')

@section('content')
<div class="w-full space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

    {{-- Sambutan & identitas singkat --}}
    <section class="grid items-end gap-5 lg:grid-cols-[minmax(0,1.5fr)_minmax(16rem,0.8fr)]">
        <div class="space-y-2.5">
            <p class="label-caps">Dasbor pegawai</p>
            <h2 class="font-display text-3xl leading-tight text-slate-900 dark:text-white sm:text-[2.5rem]">
                Selamat datang, {{ explode(' ', $user->name)[0] }}
            </h2>
            <p class="max-w-2xl text-sm text-slate-600 dark:text-slate-300">
                {{ $user->job_title ?? 'Pegawai Kampus Pangudi Luhur Deltamas' }}
            </p>
            <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    {{ strtoupper(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'STAF')) }}
                </span>
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 font-medium text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300">
                    <i class="bi bi-calendar-check text-gold-500"></i>
                    2026/2027 · Semester Ganjil
                </span>
            </div>
        </div>

        <dl class="portal-card grid grid-cols-1 overflow-hidden text-sm sm:grid-cols-3 lg:grid-cols-1">
            <div class="border-b border-slate-200 px-4 py-3 last:border-0 dark:border-slate-800 sm:border-b-0 sm:border-r sm:last:border-r-0 lg:border-b lg:border-r-0">
                <dt class="label-caps">NIP</dt>
                <dd class="mt-1 font-mono font-semibold text-slate-900 dark:text-white">{{ $user->nip ?? '-' }}</dd>
            </div>
            <div class="border-b border-slate-200 px-4 py-3 dark:border-slate-800 sm:border-b-0 sm:border-r lg:border-b lg:border-r-0">
                <dt class="label-caps">Penempatan</dt>
                <dd class="mt-1 font-semibold text-slate-900 dark:text-white">
                    {{ $user->units->isNotEmpty() ? $user->units->pluck('code')->implode(' & ') : 'Kampus' }}
                </dd>
            </div>
            <div class="px-4 py-3">
                <dt class="label-caps">Waktu</dt>
                <dd class="mt-1 font-mono text-slate-700 dark:text-slate-200">{{ now()->format('d M Y, H:i') }} WIB</dd>
            </div>
        </dl>
    </section>

    {{-- Kartu ringkasan angka --}}
    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @php
            $statCards = [
                ['label' => 'Pengumuman Belum Dibaca', 'value' => $stats['unread_announcements'], 'icon' => 'bi-megaphone', 'route' => 'announcements.index'],
                ['label' => 'Pesan Belum Dibuka', 'value' => $stats['unread_messages'], 'icon' => 'bi-chat-left-text', 'route' => 'messages.index'],
                ['label' => 'Izin Menunggu Validasi', 'value' => $stats['pending_leaves'], 'icon' => 'bi-file-earmark-check', 'route' => 'leaves.index'],
                ['label' => 'Reservasi Fasilitas Aktif', 'value' => $stats['active_bookings'], 'icon' => 'bi-building', 'route' => 'facilities.index'],
            ];
        @endphp
        @foreach($statCards as $card)
            <a href="{{ route($card['route']) }}" class="portal-card group p-4 transition hover:border-gold-400 hover:shadow-card-hover">
                <div class="flex items-start justify-between gap-2">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-ink-900 text-base text-gold-300">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </span>
                    <i class="bi bi-arrow-up-right text-xs text-slate-600 dark:text-slate-400 transition group-hover:text-gold-500"></i>
                </div>
                <p class="mt-3 font-display text-3xl leading-none text-slate-900 dark:text-white">{{ $card['value'] }}</p>
                <p class="mt-1.5 text-[11px] font-medium leading-snug text-slate-600 dark:text-slate-300">{{ $card['label'] }}</p>
            </a>
        @endforeach
    </section>

    {{-- Feed: Pengumuman terbaru & Agenda terdekat --}}
    <section class="grid gap-4 lg:grid-cols-2">
        <article class="portal-card overflow-hidden">
            <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div>
                    <h2 class="font-display text-base text-slate-900 dark:text-white">Pengumuman terbaru</h2>
                    <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">Broadcast resmi kampus untuk seluruh pegawai</p>
                </div>
                <a href="{{ route('announcements.index') }}" class="inline-flex shrink-0 items-center gap-1 text-xs font-medium text-slate-600 dark:text-slate-300 transition hover:text-slate-900 dark:hover:text-white">
                    Semua <i class="bi bi-chevron-right text-[10px]"></i>
                </a>
            </header>

            <ul class="feed-divide">
                @forelse($latestAnnouncements as $ann)
                    @php $isUnread = ! in_array($ann->id, $readAnnouncementIds); @endphp
                    <li>
                        <a href="{{ route('announcements.index') }}" class="block px-5 py-4 transition hover:bg-slate-50 dark:hover:bg-white/5">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg {{ $ann->is_urgent ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300' }}">
                                    <i class="bi {{ $ann->is_urgent ? 'bi-exclamation-triangle-fill' : 'bi-megaphone' }} text-sm"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="rounded px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide {{ $ann->scope === 'campus' ? 'bg-gold-500/15 text-gold-700 dark:text-gold-300' : 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-200' }}">
                                            {{ $ann->scope === 'campus' ? 'Kampus' : 'Unit '.($ann->unit_code ?? '-') }}
                                        </span>
                                        @if($isUnread)
                                            <span class="rounded-full bg-rose-700 px-1.5 py-0.5 text-[9px] font-bold uppercase text-white dark:bg-rose-600">Baru</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm leading-snug text-slate-800 dark:text-slate-100">{{ $ann->body }}</p>
                                    <p class="mt-1 font-mono text-[10px] text-slate-600 dark:text-slate-400">
                                        {{ $ann->sender_name }} · {{ \Carbon\Carbon::parse($ann->publish_at)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center">
                        <i class="bi bi-megaphone text-2xl text-slate-300"></i>
                        <p class="mt-2 text-xs text-slate-600 dark:text-slate-400">Belum ada pengumuman aktif.</p>
                    </li>
                @endforelse
            </ul>
        </article>

        <article class="portal-card overflow-hidden">
            <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div>
                    <h2 class="font-display text-base text-slate-900 dark:text-white">Agenda terdekat</h2>
                    <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">Kegiatan kampus yang perlu Anda ketahui</p>
                </div>
                <a href="{{ route('agendas.index') }}" class="inline-flex shrink-0 items-center gap-1 text-xs font-medium text-slate-600 dark:text-slate-300 transition hover:text-slate-900 dark:hover:text-white">
                    Semua <i class="bi bi-chevron-right text-[10px]"></i>
                </a>
            </header>

            <ul class="feed-divide">
                @forelse($upcomingAgendas as $ag)
                    @php $start = \Carbon\Carbon::parse($ag->start_at); @endphp
                    <li>
                        <a href="{{ route('agendas.index') }}" class="flex items-start gap-3 px-5 py-4 transition hover:bg-slate-50 dark:hover:bg-white/5">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl border border-slate-200 bg-slate-50 text-center dark:border-white/10 dark:bg-white/5">
                                <span class="block font-display text-base leading-none text-slate-900 dark:text-white">{{ $start->format('d') }}</span>
                                <span class="mt-0.5 block text-[9px] font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-400">{{ $start->translatedFormat('M') }}</span>
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="rounded px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide {{ $ag->category === 'campus' ? 'bg-gold-500/15 text-gold-700 dark:text-gold-300' : 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-200' }}">
                                        {{ $ag->category === 'campus' ? 'Kampus' : ($ag->category === 'unit' ? 'Unit '.($ag->unit_code ?? '-') : 'Pribadi') }}
                                    </span>
                                </div>
                                <p class="mt-1 truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $ag->title }}</p>
                                <p class="mt-0.5 font-mono text-[10px] text-slate-600 dark:text-slate-400">
                                    {{ $start->translatedFormat('D, H:i') }} WIB
                                    @if($ag->facility_name) · {{ $ag->facility_name }}
                                    @elseif($ag->location_text) · {{ $ag->location_text }}
                                    @endif
                                </p>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center">
                        <i class="bi bi-calendar3 text-2xl text-slate-300"></i>
                        <p class="mt-2 text-xs text-slate-600 dark:text-slate-400">Tidak ada agenda mendatang.</p>
                    </li>
                @endforelse
            </ul>
        </article>
    </section>

    {{-- Banner notifikasi smartphone --}}
    <section class="portal-card flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
        <div class="flex items-start gap-3">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-ink-900 text-lg text-gold-300">
                <i class="bi bi-bell"></i>
            </span>
            <div>
                <h3 class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                    <span>Notifikasi Smartphone (Web Push PWA)</span>
                    <span class="rounded-full bg-gold-500/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-gold-700 dark:text-gold-300">Baru</span>
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
            <button type="button" onclick="handleSendTestPush()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 transition hover:bg-slate-50 dark:border-white/15 dark:bg-white/10 dark:text-slate-100 dark:hover:bg-white/15">
                <i class="bi bi-send-check"></i>
                <span>Tes Notifikasi</span>
            </button>
        </div>
    </section>

    {{-- Akses menu cepat --}}
    <section class="space-y-3">
        <div>
            <p class="label-caps">Akses cepat</p>
            <h3 class="mt-1 font-display text-xl text-slate-900 dark:text-white">Layanan pegawai</h3>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $shortcuts = [
                    ['route' => 'announcements.index', 'icon' => 'bi-megaphone', 'title' => 'Pengumuman', 'hint' => 'Broadcast resmi'],
                    ['route' => 'agendas.index', 'icon' => 'bi-calendar3', 'title' => 'Agenda Kampus', 'hint' => 'Jadwal kegiatan'],
                    ['route' => 'leaves.index', 'icon' => 'bi-file-earmark-check', 'title' => 'Perizinan Staf', 'hint' => 'Izin & guru inval'],
                    ['route' => 'facilities.index', 'icon' => 'bi-building', 'title' => 'Reservasi Fasilitas', 'hint' => 'Kendaraan & ruang'],
                    ['route' => 'operational.index', 'icon' => 'bi-tools', 'title' => 'Operasional', 'hint' => 'Tugas, tiket, tamu'],
                    ['route' => 'letters.index', 'icon' => 'bi-envelope-paper', 'title' => 'Tata Persuratan', 'hint' => 'Arsip & no. surat'],
                    ['route' => 'messages.index', 'icon' => 'bi-chat-left-text', 'title' => 'Pesan', 'hint' => 'Satu arah'],
                ];
            @endphp
            @foreach($shortcuts as $item)
                <a href="{{ route($item['route']) }}" class="portal-card group flex items-center gap-3 p-4 transition hover:border-gold-400 hover:shadow-card-hover">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-slate-100 text-lg text-blue-800 transition group-hover:bg-ink-900 group-hover:text-gold-300 dark:bg-white/10 dark:text-blue-300">
                        <i class="bi {{ $item['icon'] }}"></i>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-slate-900 dark:text-white">{{ $item['title'] }}</span>
                        <span class="block text-xs text-slate-600 dark:text-slate-400">{{ $item['hint'] }}</span>
                    </span>
                </a>
            @endforeach
            <button type="button" onclick="showDevelopmentNotice()" class="group flex items-center gap-3 rounded-xl border border-dashed border-slate-300 bg-white/70 p-4 text-left transition hover:border-gold-400 dark:border-white/15 dark:bg-white/5">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-slate-100 text-lg text-blue-800 dark:bg-white/10 dark:text-blue-300">
                    <i class="bi bi-book-half"></i>
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-slate-900 dark:text-white">Kurikulum</span>
                    <span class="block text-xs text-slate-600 dark:text-slate-400">Akademik &amp; nilai</span>
                </span>
            </button>
        </div>
    </section>

    {{-- Panel informasi akun, RBAC, status modul --}}
    <section class="grid gap-4 lg:grid-cols-3">
        <article class="portal-card p-5">
            <h3 class="label-caps">Informasi Akun Pegawai</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 dark:border-slate-800">
                    <dt class="text-slate-600 dark:text-slate-400">Peran Utama</dt>
                    <dd class="text-right font-semibold text-slate-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'Staf')) }}</dd>
                </div>
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 dark:border-slate-800">
                    <dt class="text-slate-600 dark:text-slate-400">Kategori</dt>
                    <dd class="text-right font-medium text-slate-900 dark:text-white">{{ ucfirst($user->staff_category ?? 'Umum') }}</dd>
                </div>
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 dark:border-slate-800">
                    <dt class="text-slate-600 dark:text-slate-400">Penempatan</dt>
                    <dd class="text-right font-medium text-slate-900 dark:text-white">
                        {{ $user->units->isNotEmpty() ? $user->units->pluck('name')->implode(', ') : 'Kampus Deltamas' }}
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-600 dark:text-slate-400">Status Akun</dt>
                    <dd class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">Aktif</dd>
                </div>
            </dl>
        </article>

        <article class="portal-card p-5">
            <h3 class="label-caps">Hak Akses Otorisasi (RBAC)</h3>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                Akun ini memiliki <strong class="font-mono text-slate-900 dark:text-white">{{ $user->getAllPermissions()->count() }} izin aktif</strong> yang diatur melalui paket <code class="font-mono text-blue-700 dark:text-blue-300">spatie/laravel-permission</code>.
            </p>
            <div class="mt-4 flex max-h-40 flex-wrap gap-1.5 overflow-y-auto">
                @foreach($user->getAllPermissions() as $p)
                    <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 font-mono text-[11px] text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-300">
                        <i class="bi bi-check2 text-emerald-600"></i>
                        {{ $p->name }}
                    </span>
                @endforeach
            </div>
        </article>

        <article class="portal-card p-5">
            <h3 class="label-caps">Status Modul Sistem</h3>
            <ul class="mt-4 space-y-2 text-sm">
                <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-3 dark:bg-white/5">
                    <span class="font-medium text-slate-800 dark:text-slate-200">Komunikasi & Fasilitas</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                </li>
                <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-3 dark:bg-white/5">
                    <span class="font-medium text-slate-800 dark:text-slate-200">Perizinan & Operasional</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                </li>
                <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-3 dark:bg-white/5">
                    <span class="font-medium text-slate-800 dark:text-slate-200">Modul Kurikulum</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-gold-600 dark:text-gold-300"><i class="bi bi-info-circle-fill"></i> Pengembangan</span>
                </li>
            </ul>
        </article>
    </section>
</div>
@endsection
