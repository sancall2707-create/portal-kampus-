@extends('layouts.app')

@section('title', 'Kalender & Agenda — SIAP PL Deltamas')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header Action & Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span>Kalender Agenda Kampus</span>
                <span class="text-xs font-mono font-normal px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                    {{ $agendas->count() }} Kegiatan
                </span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Jadwal resmi terpadu acara kampus, unit sekolah, dan agenda pribadi.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            @if($canCreate)
                <a href="{{ route('agendas.create') }}" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>Jadwalkan Agenda</span>
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

    <!-- Filter Pills -->
    <div class="flex items-center space-x-2 text-xs overflow-x-auto pb-1">
        <a href="{{ route('agendas.index', ['category' => 'all']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $category === 'all' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Semua Agenda
        </a>
        <a href="{{ route('agendas.index', ['category' => 'campus']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $category === 'campus' ? 'bg-purple-600 border-purple-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Tingkat Kampus
        </a>
        <a href="{{ route('agendas.index', ['category' => 'unit']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $category === 'unit' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Tingkat Unit
        </a>
        <a href="{{ route('agendas.index', ['category' => 'personal']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $category === 'personal' ? 'bg-emerald-600 border-emerald-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Agenda Pribadi
        </a>
    </div>

    <!-- Agenda List -->
    <div class="space-y-4">
        @forelse($agendas as $item)
            @php
                $start = \Carbon\Carbon::parse($item->start_at);
                $end = \Carbon\Carbon::parse($item->end_at);
                $isSameDay = $start->isSameDay($end);
            @endphp
            <div class="rounded-2xl border {{ $item->blocks_booking ? 'border-amber-300 dark:border-amber-500/40 bg-amber-50/40 dark:bg-amber-950/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/50' }} p-5 sm:p-6 space-y-3 shadow-sm dark:shadow-xl backdrop-blur transition hover:border-slate-300 dark:hover:border-slate-700">

                <!-- Meta Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800/80 pb-3">
                    <div class="flex items-center space-x-2.5 flex-wrap gap-y-1">
                        @if($item->category === 'campus')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">
                                Agenda Kampus
                            </span>
                        @elseif($item->category === 'unit')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                                Unit {{ $item->unit_code }}
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                                Pribadi
                            </span>
                        @endif

                        @if($item->blocks_booking)
                            <span class="px-2 py-0.5 rounded-md text-[10px] uppercase font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30 flex items-center gap-1" title="Agenda ini mengunci fasilitas kampus sehingga menolak reservasi lain yang bentrok">
                                <i class="bi bi-lock-fill"></i>
                                <span>Memblokir Reservasi</span>
                            </span>
                        @endif
                    </div>

                    <!-- Date & Time Info -->
                    <div class="text-xs font-mono text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-3 py-1 rounded-lg shrink-0 flex items-center gap-2">
                        <i class="bi bi-clock-fill"></i>
                        @if($isSameDay)
                            <span>{{ $start->translatedFormat('d M Y') }} · {{ $start->format('H:i') }} - {{ $end->format('H:i') }} WIB</span>
                        @else
                            <span>{{ $start->translatedFormat('d M H:i') }} s.d. {{ $end->translatedFormat('d M H:i') }} WIB</span>
                        @endif
                    </div>
                </div>

                <!-- Agenda Title -->
                <div class="space-y-1">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ $item->title }}
                    </h3>
                    @if($item->description)
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                            {{ $item->description }}
                        </p>
                    @endif
                </div>

                <!-- Location & Creator -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800/80 text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex items-center space-x-2">
                        <i class="bi bi-geo-alt-fill text-blue-600 dark:text-blue-400 shrink-0"></i>
                        <span>
                            Lokasi: <strong class="text-slate-800 dark:text-white">{{ $item->facility_name ?? ($item->location_text ?? 'Kampus PL Deltamas') }}</strong>
                        </span>
                    </div>

                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                        Penyelenggara: <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $item->creator_name }}</span> ({{ $item->creator_title }})
                    </div>
                </div>

            </div>
        @empty
            <div class="text-center py-12 border border-dashed border-slate-300 dark:border-slate-800 rounded-2xl bg-white/40 dark:bg-slate-850/40 p-8 space-y-3">
                <i class="bi bi-calendar-x text-3xl text-slate-400"></i>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Tidak ada agenda untuk kategori ini</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Agenda kegiatan baru yang ditambahkan pimpinan atau panitia akan otomatis tampil di kalender ini.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
