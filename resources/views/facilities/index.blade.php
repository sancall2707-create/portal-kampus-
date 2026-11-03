@extends('layouts.app')

@section('title', 'Fasilitas & Reservasi — SIAP PL Deltamas')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span>Fasilitas & Reservasi Kampus</span>
                <span class="text-xs font-mono font-normal px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                    {{ $facilities->count() }} Fasilitas
                </span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Katalog sarana prasarana kampus (kendaraan, aula, tempat ibadat), validasi bentrok jadwal, dan penerbitan e-ticket.
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

    <!-- Main Navigation Tabs (Bookings vs Catalog) -->
    <div class="flex items-center space-x-3 border-b border-slate-200 dark:border-slate-800">
        <a href="{{ route('facilities.index', ['tab' => 'bookings']) }}" 
           class="pb-3 text-xs font-bold transition flex items-center gap-2 border-b-2 {{ $tab === 'bookings' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
            <i class="bi bi-calendar-check"></i>
            <span>Jadwal & Riwayat Peminjaman</span>
        </a>
        <a href="{{ route('facilities.index', ['tab' => 'catalog']) }}" 
           class="pb-3 text-xs font-bold transition flex items-center gap-2 border-b-2 {{ $tab === 'catalog' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
            <i class="bi bi-building"></i>
            <span>Katalog Master Fasilitas</span>
        </a>
    </div>

    @if($tab === 'bookings')
        <!-- TAB 1: LIST BOOKINGS -->
        <div class="space-y-4">
            <!-- Filter Pills -->
            <div class="flex items-center space-x-2 text-xs overflow-x-auto pb-1">
                <a href="{{ route('facilities.index', ['tab' => 'bookings', 'status' => 'all']) }}" 
                   class="px-3 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $statusFilter === 'all' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Semua
                </a>
                <a href="{{ route('facilities.index', ['tab' => 'bookings', 'status' => 'pending_screening']) }}" 
                   class="px-3 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $statusFilter === 'pending_screening' ? 'bg-cyan-600 border-cyan-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Screening TU
                </a>
                <a href="{{ route('facilities.index', ['tab' => 'bookings', 'status' => 'pending_unit']) }}" 
                   class="px-3 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $statusFilter === 'pending_unit' ? 'bg-amber-600 border-amber-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Menunggu Kepsek
                </a>
                <a href="{{ route('facilities.index', ['tab' => 'bookings', 'status' => 'pending_coordinator']) }}" 
                   class="px-3 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $statusFilter === 'pending_coordinator' ? 'bg-purple-600 border-purple-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Menunggu Koordinator
                </a>
                <a href="{{ route('facilities.index', ['tab' => 'bookings', 'status' => 'approved']) }}" 
                   class="px-3 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $statusFilter === 'approved' ? 'bg-emerald-600 border-emerald-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    Disetujui (E-Ticket Aktif)
                </a>
            </div>

            <!-- Table of Bookings -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/40 shadow-sm dark:shadow-xl backdrop-blur transition-colors">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700 font-semibold">
                        <tr>
                            <th class="py-3 px-4">Kode Booking</th>
                            <th class="py-3 px-4">Fasilitas / Ruang</th>
                            <th class="py-3 px-4">Pemohon</th>
                            <th class="py-3 px-4">Waktu Pelaksanaan</th>
                            <th class="py-3 px-4">Status Pengajuan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($bookings as $b)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                                <td class="py-3 px-4 font-mono font-bold text-blue-600 dark:text-blue-400">
                                    {{ $b->booking_code }}
                                    @if($b->is_external)
                                        <div class="text-[9px] uppercase font-bold text-purple-600 dark:text-purple-400">Eksternal</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $b->facility_name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $b->purpose }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $b->requester_name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $b->organization_name ?? 'Internal Sekolah' }}</div>
                                </td>
                                <td class="py-3 px-4 font-mono">
                                    <div class="text-slate-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($b->start_at)->translatedFormat('d M Y') }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($b->start_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($b->end_at)->format('H:i') }} WIB</div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($b->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                                            <i class="bi bi-qr-code"></i>
                                            <span>Disetujui Resmi</span>
                                        </span>
                                    @elseif($b->status === 'pending_screening')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-cyan-100 dark:bg-cyan-500/20 text-cyan-800 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-500/30">
                                            <i class="bi bi-search"></i>
                                            <span>Screening TU</span>
                                        </span>
                                    @elseif($b->status === 'pending_unit')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                            <i class="bi bi-hourglass-split"></i>
                                            <span>Validasi Kepsek</span>
                                        </span>
                                    @elseif($b->status === 'pending_coordinator')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">
                                            <i class="bi bi-hourglass-split"></i>
                                            <span>Persetujuan Koordinator</span>
                                        </span>
                                    @elseif($b->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                                            <i class="bi bi-x-circle"></i>
                                            <span>Ditolak</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('facilities.show', $b->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-300 dark:border-slate-700 font-semibold text-[11px] transition inline-flex items-center gap-1">
                                        <span>Detail</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <i class="bi bi-inbox text-3xl"></i>
                                    <div class="mt-2 text-xs font-semibold">Tidak ada reservasi fasilitas pada kategori ini.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- TAB 2: CATALOG OF FACILITIES (FC-01) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($facilities as $f)
                <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-6 space-y-4 shadow-sm dark:shadow-xl transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                                    {{ $f->category_name }}
                                </span>
                                @if($f->scope === 'campus')
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">
                                        Skala Kampus
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                        Unit {{ $f->unit_code }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mt-1.5">{{ $f->name }}</h3>
                        </div>

                        <div>
                            @if($f->status === 'active')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                                    Siap Pakai
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                    Maintenance
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        {{ $f->description ?? 'Fasilitas resmi penunjang kegiatan Kampus Pangudi Luhur Deltamas.' }}
                    </p>

                    <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-500 dark:text-slate-400 font-mono bg-slate-50 dark:bg-slate-900 p-3 rounded-xl border border-slate-200 dark:border-slate-800">
                        <div>Kapasitas: <span class="text-slate-900 dark:text-white font-bold">{{ $f->capacity ? "~{$f->capacity} orang" : '-' }}</span></div>
                        <div>Akses: <span class="text-slate-900 dark:text-white font-bold">{{ $f->visibility === 'public' ? 'Bisa Dipinjam Umum' : 'Hanya Internal' }}</span></div>
                    </div>

                    @if($f->rules_text)
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">
                            <strong>Tata Tertib:</strong> {{ $f->rules_text }}
                        </div>
                    @endif

                    <div class="pt-2">
                        <a href="{{ route('facilities.create', ['facility_id' => $f->id]) }}" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5">
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
