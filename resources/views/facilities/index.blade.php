@extends('layouts.app')

@section('title', 'Fasilitas & Reservasi — SIAP PL Deltamas')

@section('content')
<div class="w-full px-4 py-6 sm:px-6 sm:py-8 lg:px-8 space-y-7">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h2 class="font-display text-2xl sm:text-3xl font-semibold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span>Fasilitas & Reservasi Kampus</span>
                <span class="text-xs font-mono font-normal px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                    {{ $facilities->count() }} Fasilitas
                </span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Katalog sarana prasarana, kalender ketersediaan jadwal, validasi bentrok atomik, dan penerbitan e-surat izin resmi.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('facilities.create') }}" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                <i class="bi bi-calendar-plus-fill"></i>
                <span>Ajukan Peminjaman</span>
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Main Navigation Tabs (Bookings vs Calendar vs Catalog) -->
    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 border-b border-slate-200 dark:border-slate-800">
        <a href="{{ route('facilities.index', ['tab' => 'bookings']) }}" 
           class="pb-3 text-xs font-bold transition flex items-center gap-2 border-b-2 whitespace-nowrap {{ $tab === 'bookings' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
            <i class="bi bi-calendar-check"></i>
            <span>Jadwal & Riwayat Peminjaman</span>
        </a>
        <a href="{{ route('facilities.index', ['tab' => 'calendar']) }}" 
           class="pb-3 text-xs font-bold transition flex items-center gap-2 border-b-2 whitespace-nowrap {{ $tab === 'calendar' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
            <i class="bi bi-calendar-week-fill"></i>
            <span>Kalender Ketersediaan (Slot)</span>
        </a>
        <a href="{{ route('facilities.index', ['tab' => 'catalog']) }}" 
           class="pb-3 text-xs font-bold transition flex items-center gap-2 border-b-2 whitespace-nowrap {{ $tab === 'catalog' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
            <i class="bi bi-building"></i>
            <span>Katalog Master Fasilitas</span>
        </a>
    </div>

    @if($tab === 'bookings')
        <!-- TAB 1: LIST BOOKINGS -->
        <div class="space-y-4">
            <!-- Filter Pills -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <a href="{{ route('facilities.index', ['tab' => 'bookings', 'status' => 'all']) }}" 
                   class="px-3 py-1.5 rounded-lg border font-semibold transition shrink-0 whitespace-nowrap {{ $statusFilter === 'all' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Semua ({{ $bookings->count() }})
                </a>
                <a href="{{ route('facilities.index', ['tab' => 'bookings', 'status' => 'pending_screening']) }}" 
                   class="px-3 py-1.5 rounded-lg border font-semibold transition shrink-0 whitespace-nowrap {{ $statusFilter === 'pending_screening' ? 'bg-cyan-600 border-cyan-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Screening TU
                </a>
                <a href="{{ route('facilities.index', ['tab' => 'bookings', 'status' => 'pending_unit']) }}" 
                   class="px-3 py-1.5 rounded-lg border font-semibold transition shrink-0 whitespace-nowrap {{ $statusFilter === 'pending_unit' ? 'bg-amber-600 border-amber-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Menunggu Kepsek (Tier 1)
                </a>
                <a href="{{ route('facilities.index', ['tab' => 'bookings', 'status' => 'pending_coordinator']) }}" 
                   class="px-3 py-1.5 rounded-lg border font-semibold transition shrink-0 whitespace-nowrap {{ $statusFilter === 'pending_coordinator' ? 'bg-purple-600 border-purple-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Menunggu Koordinator (Tier 2)
                </a>
                <a href="{{ route('facilities.index', ['tab' => 'approved']) }}" 
                   class="px-3 py-1.5 rounded-lg border font-semibold transition shrink-0 whitespace-nowrap {{ $statusFilter === 'approved' ? 'bg-emerald-600 border-emerald-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Disetujui Sah (E-Ticket Aktif)
                </a>
                <a href="{{ route('facilities.index', ['tab' => 'bookings', 'status' => 'rejected']) }}" 
                   class="px-3 py-1.5 rounded-lg border font-semibold transition shrink-0 whitespace-nowrap {{ $statusFilter === 'rejected' ? 'bg-rose-600 border-rose-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Ditolak
                </a>
            </div>

            <!-- Table of Bookings -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 shadow-sm dark:shadow-xl transition-colors">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700 font-semibold">
                        <tr>
                            <th class="py-3 px-4">Kode / No. Surat</th>
                            <th class="py-3 px-4">Fasilitas / Acara</th>
                            <th class="py-3 px-4">Pemohon & PIC</th>
                            <th class="py-3 px-4">Jadwal Penggunaan</th>
                            <th class="py-3 px-4">Status Otorisasi</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                        @forelse($bookings as $b)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/60 transition">
                                <td class="py-3 px-4 font-mono font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                    {{ $b->booking_code }}
                                    @if($b->letter_number)
                                        <div class="text-[10px] text-slate-400 font-normal">{{ $b->letter_number }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $b->facility_name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $b->event_name ?? $b->purpose }}</div>
                                    @if($b->destination)
                                        <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono">Tujuan: {{ $b->destination }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $b->requester_name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">PIC: {{ $b->pic_name ?? '-' }} ({{ $b->pic_phone ?? '-' }})</div>
                                </td>
                                <td class="py-3 px-4 font-mono whitespace-nowrap">
                                    <div class="text-slate-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($b->start_at)->translatedFormat('d M Y') }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($b->start_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($b->end_at)->format('H:i') }} WIB</div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($b->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                                            <i class="bi bi-qr-code"></i>
                                            <span>Disetujui Sah</span>
                                        </span>
                                    @elseif($b->status === 'pending_screening')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-cyan-100 dark:bg-cyan-500/20 text-cyan-800 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-500/30">
                                            <i class="bi bi-search"></i>
                                            <span>Screening TU</span>
                                        </span>
                                    @elseif($b->status === 'pending_unit')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                            <i class="bi bi-hourglass-split"></i>
                                            <span>Tier 1 (Kepsek)</span>
                                        </span>
                                    @elseif($b->status === 'pending_coordinator')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">
                                            <i class="bi bi-hourglass-split"></i>
                                            <span>Tier 2 (Sarpras)</span>
                                        </span>
                                    @elseif($b->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                                            <i class="bi bi-x-circle"></i>
                                            <span>Ditolak</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap space-x-1">
                                    <a href="{{ route('facilities.show', $b->id) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-300 dark:border-slate-600 font-semibold text-[11px] transition inline-flex items-center gap-1">
                                        <span>Detail</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                    @if($b->status === 'approved')
                                        <a href="{{ route('facilities.letter', $b->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-500/20 hover:bg-blue-100 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30 font-semibold text-[11px] transition inline-flex items-center gap-1" title="Cetak Surat Izin">
                                            <i class="bi bi-printer-fill"></i>
                                            <span>Surat</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <i class="bi bi-inbox text-3xl"></i>
                                    <div class="mt-2 text-xs font-semibold">Tidak ada data peminjaman fasilitas pada filter ini.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($tab === 'calendar')
        <!-- TAB 2: KALENDER KETERSEDIAAN INTERAKTIF (PRD Migrasi 5.1) -->
        <div class="space-y-4">
            <!-- Legend Indicators -->
            <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                    <i class="bi bi-info-circle-fill text-blue-600 dark:text-blue-400"></i>
                    <span>Indikator Slot Ketersediaan:</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600 dark:text-slate-300">Hijau: Tersedia</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                        <span class="text-slate-600 dark:text-slate-300">Kuning: Sedang Diverifikasi</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded-full bg-rose-500"></span>
                        <span class="text-slate-600 dark:text-slate-300">Merah: Telah Dipesan Sah</span>
                    </div>
                </div>
            </div>

            <!-- Visual Timeline Grid (Fasilitas x 7 Hari) -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm transition-colors">
                <table class="w-full text-left text-xs border-collapse bg-white dark:bg-slate-800">
                    <thead class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700 font-semibold">
                        <tr>
                            <th class="py-3.5 px-4 min-w-[200px] text-slate-800 dark:text-slate-200">Nama Sarpras / Fasilitas</th>
                            @foreach($calendarDays as $day)
                                <th class="py-3.5 px-3 text-center min-w-[120px] {{ $day->isToday() ? 'bg-blue-100/70 dark:bg-blue-950/80 text-blue-900 dark:text-blue-300 font-bold border-x border-blue-200 dark:border-blue-800/40' : 'text-slate-700 dark:text-slate-300' }}">
                                    <div class="leading-tight">{{ $day->translatedFormat('D') }}</div>
                                    <div class="text-[10px] font-mono font-normal {{ $day->isToday() ? 'text-blue-800 dark:text-blue-300 font-bold' : 'text-slate-500 dark:text-slate-400' }}">{{ $day->format('d M') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800">
                        @foreach($facilities as $f)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                <td class="py-3.5 px-4 bg-white dark:bg-slate-800">
                                    <div class="font-bold text-slate-900 dark:text-white leading-snug">{{ $f->name }}</div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $f->category_name }} · {{ $f->scope === 'campus' ? 'Skala Kampus' : 'Unit ' . $f->unit_code }}</div>
                                </td>
                                @foreach($calendarDays as $day)
                                    @php
                                        $dayStr = $day->toDateString();
                                        $bookingsOnThisDay = $activeBookingsForCalendar->filter(function($b) use ($f, $dayStr) {
                                            if ($b->facility_id != $f->id) return false;
                                            $bStart = substr($b->start_at, 0, 10);
                                            $bEnd = substr($b->end_at, 0, 10);
                                            return ($dayStr >= $bStart && $dayStr <= $bEnd);
                                        });
                                    @endphp
                                    <td class="py-3 px-2 text-center align-middle {{ $day->isToday() ? 'bg-blue-50/40 dark:bg-blue-950/30 border-x border-blue-100 dark:border-blue-900/30' : 'bg-white dark:bg-slate-800' }}">
                                        @if($bookingsOnThisDay->isEmpty())
                                            <a href="{{ route('facilities.create', ['facility_id' => $f->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 text-[10px] font-bold transition" title="Fasilitas Kosong - Klik untuk Pesan">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                <span>Tersedia</span>
                                            </a>
                                        @else
                                            @foreach($bookingsOnThisDay as $bd)
                                                @if($bd->status === 'approved')
                                                    <div class="p-1 rounded-lg bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-[10px] font-bold leading-tight my-0.5 shadow-xs">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500 inline-block mr-1"></span>
                                                        <span>Dipesan</span>
                                                    </div>
                                                @else
                                                    <div class="p-1 rounded-lg bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-500/30 text-amber-800 dark:text-amber-300 text-[10px] font-bold leading-tight my-0.5 shadow-xs">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 inline-block mr-1"></span>
                                                        <span>Verifikasi</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- TAB 3: CATALOG OF FACILITIES (FC-01) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($facilities as $f)
                <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 space-y-4 shadow-sm dark:shadow-xl transition-colors">
                    
                    <div class="flex items-start justify-between gap-3 border-b border-slate-100 dark:border-slate-700 pb-3">
                        <div class="space-y-1.5 flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30 whitespace-nowrap">
                                    {{ $f->category_name }}
                                </span>
                                @if($f->scope === 'campus')
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30 whitespace-nowrap">
                                        Skala Kampus
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 whitespace-nowrap">
                                        Unit {{ $f->unit_code }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-snug break-words">
                                {{ $f->name }}
                            </h3>
                        </div>

                        <!-- Status Badge -->
                        <div class="shrink-0 pt-0.5">
                            @if($f->status === 'active')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] uppercase font-extrabold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 whitespace-nowrap shadow-xs">
                                    <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
                                    <span>Siap Pakai</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] uppercase font-extrabold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30 whitespace-nowrap shadow-xs">
                                    <i class="bi bi-tools text-amber-600 dark:text-amber-400"></i>
                                    <span>Maintenance</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        {{ $f->description ?? 'Fasilitas resmi penunjang kegiatan Kampus Pangudi Luhur Deltamas.' }}
                    </p>

                    <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-500 dark:text-slate-400 font-mono bg-slate-50 dark:bg-slate-900 p-3 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <div>Kapasitas: <span class="text-slate-900 dark:text-white font-bold">{{ $f->capacity ? "~{$f->capacity} orang" : '-' }}</span></div>
                        <div>Akses: <span class="text-slate-900 dark:text-white font-bold">{{ $f->visibility === 'public' ? 'Bisa Dipinjam Umum' : 'Hanya Internal' }}</span></div>
                    </div>

                    @if($f->rules_text)
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/50 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800">
                            <strong class="text-slate-700 dark:text-slate-300">Tata Tertib:</strong> {{ $f->rules_text }}
                        </div>
                    @endif

                    <div class="pt-2">
                        <a href="{{ route('facilities.create', ['facility_id' => $f->id]) }}" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5">
                            <i class="bi bi-calendar-plus"></i>
                            <span>Pilih Fasilitas Ini</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
