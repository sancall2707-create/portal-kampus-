@extends('layouts.app')

@section('title', 'Pesan Satu Arah Pribadi — SIAP PL Deltamas')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span>Pesan Satu Arah Pribadi</span>
                <span class="text-xs font-mono font-normal px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                    Rahasia & Terbatas
                </span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Komunikasi dinas satu arah antar pegawai. Pesan bersifat privat dan hanya dapat dibaca oleh pengirim dan penerima.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('messages.create') }}" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                <i class="bi bi-pencil-square"></i>
                <span>Tulis Pesan Pribadi</span>
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

    <!-- Navigation Tabs (Kotak Masuk vs Pesan Terkirim) -->
    <div class="flex items-center space-x-4 border-b border-slate-200 dark:border-slate-800">
        <a href="{{ route('messages.index', ['tab' => 'inbox']) }}" 
           class="pb-3 text-xs font-bold transition flex items-center gap-2 border-b-2 {{ $tab === 'inbox' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
            <i class="bi bi-inbox-fill"></i>
            <span>Kotak Masuk</span>
            @if($unreadCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500 text-white">
                    {{ $unreadCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('messages.index', ['tab' => 'sent']) }}" 
           class="pb-3 text-xs font-bold transition flex items-center gap-2 border-b-2 {{ $tab === 'sent' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
            <i class="bi bi-send-fill"></i>
            <span>Pesan Terkirim</span>
        </a>
    </div>

    <!-- Daftar Pesan Pribadi -->
    <div class="space-y-3">
        @forelse($messages as $m)
            @php
                $isUnread = ($tab === 'inbox' && !$m->isRead());
                $categoryBadges = [
                    'arahan'     => 'bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-500/30',
                    'instruksi'  => 'bg-purple-100 dark:bg-purple-500/20 text-purple-800 dark:text-purple-300 border-purple-200 dark:border-purple-500/30',
                    'teguran'    => 'bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-500/30',
                    'apresiasi'  => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30',
                    'rahasia'    => 'bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 border-slate-300 dark:border-slate-600',
                    'umum'       => 'bg-cyan-100 dark:bg-cyan-500/20 text-cyan-800 dark:text-cyan-300 border-cyan-200 dark:border-cyan-500/30',
                ];
                $badgeClass = $categoryBadges[$m->category] ?? $categoryBadges['arahan'];
            @endphp

            <div class="rounded-2xl border {{ $isUnread ? 'border-blue-300 dark:border-blue-500/50 bg-blue-50/40 dark:bg-blue-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} p-4 sm:p-5 space-y-3 shadow-xs hover:shadow-md transition">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold border {{ $badgeClass }}">
                                {{ $m->category }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-400">
                                <i class="bi bi-lock-fill text-amber-500 text-[10px]"></i>
                                <span>Pribadi & Rahasia</span>
                            </span>
                            @if($isUnread)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-rose-500 text-white animate-pulse">
                                    Pesan Baru
                                </span>
                            @endif
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-snug">
                            {{ $m->subject }}
                        </h3>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-[11px] font-mono text-slate-400 block">
                            {{ $m->created_at->translatedFormat('d M Y, H:i') }}
                        </span>
                        @if($tab === 'sent')
                            @if($m->isRead())
                                <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">
                                    <i class="bi bi-check2-all text-xs font-bold"></i>
                                    <span>Dibaca {{ $m->read_at->format('d/m H:i') }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 font-medium mt-1">
                                    <i class="bi bi-check2 text-xs"></i>
                                    <span>Terkirim (Belum dibuka)</span>
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Kontak Terkait -->
                <div class="text-xs text-slate-600 dark:text-slate-300 flex items-center justify-between border-t border-slate-100 dark:border-slate-700/60 pt-2.5">
                    <div>
                        @if($tab === 'inbox')
                            <span class="text-slate-400 text-[11px]">Dari:</span>
                            <strong class="text-slate-800 dark:text-slate-200">{{ $m->sender->name }}</strong>
                            <span class="text-[10px] text-slate-400 font-mono">({{ $m->sender->job_title ?? 'Pegawai PL' }})</span>
                        @else
                            <span class="text-slate-400 text-[11px]">Kepada:</span>
                            <strong class="text-slate-800 dark:text-slate-200">{{ $m->recipient->name }}</strong>
                            <span class="text-[10px] text-slate-400 font-mono">({{ $m->recipient->job_title ?? 'Pegawai PL' }})</span>
                        @endif
                    </div>

                    <a href="{{ route('messages.show', $m->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-xs">
                        <span>Buka Pesan</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="p-12 text-center rounded-3xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 space-y-2">
                <i class="bi bi-chat-left-lock text-4xl"></i>
                <div class="text-xs font-semibold">Tidak ada pesan pribadi pada kotak ini.</div>
            </div>
        @endforelse
    </div>

</div>
@endsection
