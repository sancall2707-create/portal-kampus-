@extends('layouts.app')

@section('title', 'Tulis Pesan Satu Arah Pribadi — SIAP PL Deltamas')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">

    <div class="space-y-1">
        <a href="{{ route('messages.index') }}" class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1 transition mb-2">
            <i class="bi bi-arrow-left"></i>
            <span>Batal & Kembali ke Kotak Pesan</span>
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
            <span>Tulis Pesan Satu Arah Pribadi</span>
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400">Pesan pribadi terenkripsi internal. Hanya Anda dan penerima yang dapat membuka dan membaca pesan ini.</p>
    </div>

    <!-- Alert Validation Errors -->
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs space-y-1">
            @foreach ($errors->all() as $err)
                <p>- {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('messages.store') }}" method="POST" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 space-y-5 shadow-sm dark:shadow-2xl transition-colors">
        @csrf

        <!-- Pilih Pegawai Penerima -->
        <div>
            <label for="recipient_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Pilih Pegawai Penerima Pesan <span class="text-rose-500">*</span>
            </label>
            <select name="recipient_id" id="recipient_id" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Pegawai Tujuan --</option>
                @foreach($recipients as $rec)
                    <option value="{{ $rec->id }}" {{ old('recipient_id') == $rec->id ? 'selected' : '' }}>
                        {{ $rec->name }} — [{{ $rec->nip ?? 'Staf' }}] · {{ $rec->job_title ?? 'Pegawai' }} ({{ $rec->units->isNotEmpty() ? $rec->units->pluck('code')->implode('/') : 'Kampus' }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Kategori Pesan -->
        <div>
            <label for="category" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Kategori Pesan Dinas <span class="text-rose-500">*</span>
            </label>
            <select name="category" id="category" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="arahan" {{ old('category') == 'arahan' ? 'selected' : '' }}>Arahan Dinas Resmi</option>
                <option value="instruksi" {{ old('category') == 'instruksi' ? 'selected' : '' }}>Instruksi Tugas Tertutup</option>
                <option value="teguran" {{ old('category') == 'teguran' ? 'selected' : '' }}>Catatan Evaluasi / Kedisiplinan</option>
                <option value="apresiasi" {{ old('category') == 'apresiasi' ? 'selected' : '' }}>Apresiasi / Penghargaan Kinerja</option>
                <option value="rahasia" {{ old('category') == 'rahasia' ? 'selected' : '' }}>Pesan Sangat Rahasia (Confidential)</option>
                <option value="umum" {{ old('category') == 'umum' ? 'selected' : '' }}>Informasi Pribadi Lainnya</option>
            </select>
        </div>

        <!-- Perihal / Subjek -->
        <div>
            <label for="subject" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Perihal / Subjek Pesan <span class="text-rose-500">*</span>
            </label>
            <input 
                type="text" 
                name="subject" 
                id="subject" 
                value="{{ old('subject') }}" 
                required 
                placeholder="Contoh: Evaluasi Rombel 8B / Koordinasi Persiapan Akreditasi" 
                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <!-- Isi Pesan -->
        <div>
            <label for="body" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Isi Pesan Satu Arah <span class="text-rose-500">*</span>
            </label>
            <textarea 
                name="body" 
                id="body" 
                rows="6" 
                required 
                placeholder="Tuliskan isi instruksi, arahan, atau memo pribadi secara lengkap dan jelas..."
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-2xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition leading-relaxed"
            >{{ old('body') }}</textarea>
        </div>

        <!-- Privacy Assurance Banner -->
        <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-900 dark:text-amber-200 text-xs flex items-start gap-2.5">
            <i class="bi bi-shield-lock-fill text-amber-600 dark:text-amber-400 text-base shrink-0 mt-0.5"></i>
            <div class="space-y-0.5">
                <strong>Prinsip Kerahasiaan & Satu Arah:</strong>
                <p class="text-[11px] leading-relaxed text-amber-800 dark:text-amber-300">
                    Pesan ini dienkripsi pada basis data dan hanya bisa dibaca oleh penerima yang Anda pilih. Sistem tidak menyediakan balasan chat dua arah guna menjaga kesakralan instruksi kedinasan.
                </p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-blue-600 hover:bg-blue-500 font-bold text-sm text-white shadow-xl shadow-blue-600/30 transition flex items-center justify-center space-x-2">
                <i class="bi bi-send-check-fill"></i>
                <span>Kirim Pesan Satu Arah</span>
            </button>
        </div>
    </form>

</div>
@endsection
