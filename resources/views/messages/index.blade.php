@extends('layouts.app')

@section('title', 'Pesan — SIAP PL Deltamas')

@section('content')
<div class="w-full px-4 py-6 sm:px-6 sm:py-8 lg:px-8 space-y-7">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h2 class="font-display text-2xl sm:text-3xl font-semibold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span>Pesan Satu Arah</span>
                <span class="text-xs font-mono font-normal px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                    Maks. 50 Penerima
                </span>
            </h2>
            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                Kirim instruksi dan memo dinas satu arah secara privat ke individu atau kelompok staf terpilih (maksimal 50 orang).
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('messages.create') }}" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                <i class="bi bi-pencil-square"></i>
                <span>Tulis Pesan</span>
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
           class="pb-3 text-xs font-bold transition flex items-center gap-2 border-b-2 {{ $tab === 'inbox' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-300' }}">
            <i class="bi bi-inbox-fill"></i>
            <span>Kotak Masuk</span>
            @if($unreadCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500 text-white">
                    {{ $unreadCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('messages.index', ['tab' => 'sent']) }}" 
           class="pb-3 text-xs font-bold transition flex items-center gap-2 border-b-2 {{ $tab === 'sent' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-300' }}">
            <i class="bi bi-send-fill"></i>
            <span>Pesan Terkirim</span>
            <span class="text-[10px] font-mono text-slate-600 dark:text-slate-400">({{ count($sentBatches) }})</span>
        </a>
    </div>

    @php
        $categoryBadges = [
            'arahan'     => 'bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-500/30',
            'instruksi'  => 'bg-purple-100 dark:bg-purple-500/20 text-purple-800 dark:text-purple-300 border-purple-200 dark:border-purple-500/30',
            'teguran'    => 'bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-500/30',
            'apresiasi'  => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30',
            'rahasia'    => 'bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 border-slate-300 dark:border-slate-600',
            'umum'       => 'bg-cyan-100 dark:bg-cyan-500/20 text-cyan-800 dark:text-cyan-300 border-cyan-200 dark:border-cyan-500/30',
        ];
    @endphp

    @if($tab === 'inbox')
        <!-- TAB KOTAK MASUK -->
        <div class="space-y-3">
            @forelse($inboxMessages as $m)
                @php
                    $isUnread = !$m->isRead();
                    $badgeClass = $categoryBadges[$m->category] ?? $categoryBadges['arahan'];
                @endphp

                <div class="rounded-2xl border {{ $isUnread ? 'border-blue-300 dark:border-blue-500/50 bg-blue-50/40 dark:bg-blue-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} p-4 sm:p-5 space-y-3 shadow-xs hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold border {{ $badgeClass }}">
                                    {{ $m->category }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-600 dark:text-slate-400">
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
                            <span class="text-[11px] font-mono text-slate-600 dark:text-slate-400 block">
                                {{ $m->created_at->translatedFormat('d M Y, H:i') }}
                            </span>
                        </div>
                    </div>

                    <div class="text-xs text-slate-600 dark:text-slate-300 flex items-center justify-between border-t border-slate-100 dark:border-slate-700/60 pt-2.5">
                        <div>
                            <span class="text-slate-600 dark:text-slate-400 text-[11px]">Dari:</span>
                            <strong class="text-slate-800 dark:text-slate-200">{{ $m->sender->name }}</strong>
                            <span class="text-[10px] text-slate-600 dark:text-slate-400 font-mono">({{ $m->sender->job_title ?? 'Pegawai' }})</span>
                        </div>

                        <a href="{{ route('messages.show', $m->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-xs">
                            <span>Buka Pesan</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center rounded-3xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 space-y-2">
                    <i class="bi bi-chat-left-dots text-4xl"></i>
                    <div class="text-xs font-semibold">Tidak ada pesan masuk pada kotak ini.</div>
                </div>
            @endforelse
        </div>

    @else
        <!-- TAB PESAN TERKIRIM (Bisa Multi-Penerima s.d 50 Orang) -->
        <div class="space-y-3">
            @forelse($sentBatches as $b)
                @php
                    $badgeClass = $categoryBadges[$b->category] ?? $categoryBadges['arahan'];
                    $recNames = $b->recipients->pluck('name')->toArray();
                    $displayRec = count($recNames) > 2 
                        ? $recNames[0] . ', ' . $recNames[1] . ', dan ' . (count($recNames) - 2) . ' lainnya' 
                        : implode(', ', $recNames);
                @endphp

                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 sm:p-5 space-y-3 shadow-xs hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold border {{ $badgeClass }}">
                                    {{ $b->category }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30 font-mono">
                                    {{ $b->total_recipients }} Penerima
                                </span>
                            </div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-snug">
                                {{ $b->subject }}
                            </h3>
                        </div>

                        <div class="text-right shrink-0 space-y-1">
                            <span class="text-[11px] font-mono text-slate-600 dark:text-slate-400 block">
                                {{ $b->created_at->translatedFormat('d M Y, H:i') }}
                            </span>
                            <div class="inline-flex items-center gap-1 text-[11px] font-mono {{ $b->read_count > 0 ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-600 dark:text-slate-400' }}">
                                <i class="bi bi-check2-all text-xs"></i>
                                <span>Dibaca: {{ $b->read_count }}/{{ $b->total_recipients }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-xs text-slate-600 dark:text-slate-300 flex items-center justify-between border-t border-slate-100 dark:border-slate-700/60 pt-2.5">
                        <div class="truncate max-w-[70%]">
                            <span class="text-slate-600 dark:text-slate-400 text-[11px]">Kepada:</span>
                            <strong class="text-slate-800 dark:text-slate-200">{{ $displayRec }}</strong>
                        </div>

                        <a href="{{ route('messages.show', $b->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-xs shrink-0">
                            <span>Detail Pengiriman</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center rounded-3xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 space-y-2">
                    <i class="bi bi-send-x text-4xl"></i>
                    <div class="text-xs font-semibold">Belum ada pesan yang Anda kirimkan.</div>
                </div>
            @endforelse
        </div>
    @endif

</div>
@endsection
