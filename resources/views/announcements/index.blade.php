@extends('layouts.app')

@section('title', 'Papan Informasi & Siaran — SIAP PL Deltamas')

@section('content')
<div class="w-full px-4 py-6 sm:px-6 sm:py-8 lg:px-8 space-y-7">

    <!-- Header Action & Filters -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h2 class="font-display text-2xl sm:text-3xl font-semibold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span>Papan Siaran Resmi</span>
                <span class="text-xs font-mono font-normal px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                    {{ $announcements->count() }} Aktif
                </span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Pengumuman instruksional satu arah tingkat Kampus dan Unit sekolah.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            @if($canCreateCampus || $canCreateUnit)
                <a href="{{ route('announcements.create') }}" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>Buat Siaran Baru</span>
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

    <!-- Filter Pill Tabs -->
    <div class="flex flex-wrap items-center gap-2 text-xs">
        <a href="{{ route('announcements.index', ['filter' => 'all']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $filter === 'all' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Semua Informasi
        </a>
        <a href="{{ route('announcements.index', ['filter' => 'campus']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $filter === 'campus' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Siaran Kampus
        </a>
        <a href="{{ route('announcements.index', ['filter' => 'unit']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $filter === 'unit' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Siaran Unit Saya
        </a>
    </div>

    <!-- Announcements Feed List -->
    <div class="space-y-4">
        @forelse($announcements as $item)
            @php
                $isRead = isset($readStatus[$item->id]) && $readStatus[$item->id] !== null;
                $canTrackReceipts = $user->can('announcement.readreceipt.view') || $item->sender_id === $user->id;
            @endphp
            <div class="rounded-2xl border {{ $item->is_urgent ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-300 dark:border-rose-500/40' : 'bg-white dark:bg-slate-800/50 border-slate-200 dark:border-slate-800' }} p-5 sm:p-6 space-y-4 shadow-sm dark:shadow-xl backdrop-blur transition hover:border-slate-300 dark:hover:border-slate-700">

                <!-- Meta Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800/80 pb-3">
                    <div class="flex items-center space-x-2.5 flex-wrap gap-y-1">
                        @if($item->scope === 'campus')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">
                                Kampus PL Deltamas
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                                Unit {{ $item->unit_code }}
                            </span>
                        @endif

                        @if($item->category === 'reminder')
                            <span class="px-2 py-0.5 rounded-md text-[10px] uppercase font-semibold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                Reminder
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] uppercase font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                Pesan Baru
                            </span>
                        @endif

                        @if($item->is_urgent)
                            <span class="px-2 py-0.5 rounded-md text-[10px] uppercase font-extrabold bg-rose-100 dark:bg-rose-500/30 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-500/40 animate-pulse">
                                PENTING
                            </span>
                        @endif
                    </div>

                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono flex items-center gap-2">
                        <span>{{ \Carbon\Carbon::parse($item->publish_at)->translatedFormat('d M Y, H:i') }} WIB</span>
                        @if($item->display_until)
                            <span>· Tayang s.d. {{ \Carbon\Carbon::parse($item->display_until)->translatedFormat('d M') }}</span>
                        @endif
                    </div>
                </div>

                <!-- Message Body -->
                <div class="text-sm sm:text-base text-slate-800 dark:text-slate-200 font-medium leading-relaxed">
                    {{ $item->body }}
                </div>

                <!-- Footer: Sender & Read Status -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 text-xs">
                    <div class="flex items-center space-x-2 text-slate-500 dark:text-slate-400">
                        <span class="h-6 w-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-[10px] text-slate-700 dark:text-white">
                            {{ substr($item->sender_name, 0, 1) }}
                        </span>
                        <span>Oleh: <strong class="text-slate-700 dark:text-slate-300">{{ $item->sender_name }}</strong> ({{ $item->sender_title }})</span>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Read/Unread Marker -->
                        @if($isRead)
                            <span class="inline-flex items-center gap-1.5 text-[11px] text-emerald-700 dark:text-emerald-400 font-mono bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-2.5 py-1 rounded-lg">
                                <i class="bi bi-check2-all text-emerald-600 dark:text-emerald-400 font-bold"></i>
                                <span>Sudah Dibaca</span>
                            </span>
                        @else
                            <form action="{{ route('announcements.read', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-lg bg-blue-50 dark:bg-blue-600/20 hover:bg-blue-100 dark:hover:bg-blue-600/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/40 text-[11px] font-semibold transition flex items-center gap-1">
                                    <i class="bi bi-check2"></i>
                                    <span>Tandai Terbaca</span>
                                </button>
                            </form>
                        @endif

                        <!-- Link to Read Receipts Report -->
                        @if($canTrackReceipts)
                            <a href="{{ route('announcements.receipts', $item->id) }}" 
                               class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-300 dark:border-slate-700 text-[11px] font-semibold transition flex items-center gap-1.5"
                               title="Pantau daftar pegawai yang sudah dan belum membaca">
                                <i class="bi bi-bar-chart-fill text-blue-600 dark:text-blue-400"></i>
                                <span>Laporan Keterbacaan</span>
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="text-center py-12 border border-dashed border-slate-300 dark:border-slate-800 rounded-2xl bg-white/40 dark:bg-slate-800/40 p-8 space-y-3">
                <i class="bi bi-inbox text-3xl text-slate-400"></i>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Tidak ada siaran untuk kategori ini</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Seluruh pengumuman resmi terbaru akan ditampilkan secara real-time di halaman ini.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
