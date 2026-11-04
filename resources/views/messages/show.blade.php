@extends('layouts.app')

@section('title', $message->subject . ' — SIAP PL Deltamas')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 space-y-6">

    <!-- Header Actions -->
    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
        <a href="{{ route('messages.index') }}" class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Kotak Pesan</span>
        </a>

        <form action="{{ route('messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Hapus pesan ini dari riwayat Anda?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs text-rose-600 dark:text-rose-400 hover:text-rose-700 font-semibold transition flex items-center gap-1">
                <i class="bi bi-trash3"></i>
                <span>Hapus Pesan</span>
            </button>
        </form>
    </div>

    <!-- Confidential Memo Card -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-3xl p-6 sm:p-8 space-y-6 shadow-sm dark:shadow-2xl transition-colors">
        
        <!-- Header Memo -->
        <div class="border-b border-slate-100 dark:border-slate-700 pb-5 space-y-3">
            <div class="flex items-center justify-between gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                    <i class="bi bi-lock-fill"></i>
                    <span>Kategori: {{ ucfirst($message->category) }}</span>
                </span>
                
                <span class="text-xs font-mono text-slate-400">
                    {{ $message->created_at->translatedFormat('l, d F Y · H:i') }} WIB
                </span>
            </div>

            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-snug">
                {{ $message->subject }}
            </h1>
        </div>

        <!-- Sender & Recipient Box -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs">
            <div class="space-y-1">
                <span class="text-slate-400 uppercase tracking-wider text-[10px] font-bold block">Pemberi Pesan (Pengirim):</span>
                <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $message->sender->name }}</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $message->sender->nip ?? '-' }} · {{ $message->sender->job_title ?? 'Pegawai' }}</div>
            </div>

            <div class="space-y-1 sm:border-l sm:border-slate-200 dark:sm:border-slate-700 sm:pl-4">
                <span class="text-slate-400 uppercase tracking-wider text-[10px] font-bold block">Penerima Khusus:</span>
                <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $message->recipient->name }}</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $message->recipient->nip ?? '-' }} · {{ $message->recipient->job_title ?? 'Pegawai' }}</div>
            </div>
        </div>

        <!-- Message Body -->
        <div class="space-y-2">
            <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Isi Pesan Kedinasan:</span>
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm sm:text-base leading-relaxed text-slate-800 dark:text-slate-100 whitespace-pre-line font-sans">
                {{ $message->body }}
            </div>
        </div>

        <!-- Read Acknowledgement Status -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500 dark:text-slate-400">
            <div class="flex items-center gap-1.5 font-mono">
                <i class="bi bi-shield-check text-emerald-500 text-base"></i>
                <span>Terkonfirmasi: Hanya dapat dibaca oleh Anda & {{ $message->sender->name }}</span>
            </div>
            @if($message->isRead())
                <div class="text-emerald-600 dark:text-emerald-400 font-mono text-[11px] flex items-center gap-1">
                    <i class="bi bi-check2-all text-sm font-bold"></i>
                    <span>Telah dibaca: {{ $message->read_at->translatedFormat('d M Y, H:i') }} WIB</span>
                </div>
            @endif
        </div>
    </div>

    <!-- One-Way Policy Notice -->
    <div class="p-4 rounded-2xl bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-center text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
        <i class="bi bi-info-circle mr-1 text-blue-500"></i>
        <span>Sistem ini menerapkan prinsip <strong>One-Way Official Dispatch</strong> (komunikasi satu arah tanpa ruang balas-membalas) guna menjaga wibawa instruksi kedinasan. Jika diperlukan koordinasi lisan lebih lanjut, hubungi pengirim secara langsung.</span>
    </div>

</div>
@endsection
