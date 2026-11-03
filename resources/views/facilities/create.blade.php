@extends('layouts.app')

@section('title', 'Ajukan Peminjaman Fasilitas — SIAP PL Deltamas')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">

    <div class="space-y-1">
        <a href="{{ route('facilities.index') }}" class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1 transition mb-2">
            <i class="bi bi-arrow-left"></i>
            <span>Batal & Kembali ke Fasilitas</span>
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Formulir Peminjaman Fasilitas</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400">Pengajuan otomatis divalidasi bentrok jadwal real-time terhadap agenda kampus dan reservasi lainnya.</p>
    </div>

    <!-- Alert Validation Errors -->
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <i class="bi bi-exclamation-triangle-fill text-rose-600 dark:text-rose-400"></i>
                <span>Pengajuan Gagal / Bentrok Jadwal</span>
            </div>
            @foreach ($errors->all() as $err)
                <p>- {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <!-- Form Card (FC-04 & FC-05) -->
    <form action="{{ route('facilities.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 space-y-5 shadow-sm dark:shadow-2xl transition-colors">
        @csrf

        <!-- Pilih Fasilitas -->
        <div>
            <label for="facility_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Pilih Fasilitas / Kendaraan / Ruangan
            </label>
            <select name="facility_id" id="facility_id" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Fasilitas Kampus --</option>
                @foreach($facilities as $f)
                    <option value="{{ $f->id }}" {{ (old('facility_id', $selectedFacilityId) == $f->id) ? 'selected' : '' }}>
                        {{ $f->name }} (Kapasitas: {{ $f->capacity ? "~{$f->capacity} orang" : '-' }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Rentang Waktu -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="start_at" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Waktu Mulai Pemakaian
                </label>
                <input 
                    type="datetime-local" 
                    name="start_at" 
                    id="start_at" 
                    value="{{ old('start_at') }}" 
                    required 
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                >
            </div>
            <div>
                <label for="end_at" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Waktu Selesai Pemakaian
                </label>
                <input 
                    type="datetime-local" 
                    name="end_at" 
                    id="end_at" 
                    value="{{ old('end_at') }}" 
                    required 
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                >
            </div>
        </div>

        <!-- Jumlah Peserta -->
        <div>
            <label for="participants" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Estimasi Jumlah Peserta (Orang)
            </label>
            <input 
                type="number" 
                name="participants" 
                id="participants" 
                value="{{ old('participants') }}" 
                placeholder="Contoh: 50" 
                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
            >
        </div>

        <!-- Tujuan Pemakaian -->
        <div>
            <label for="purpose" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Tujuan Kegiatan & Rincian Acara
            </label>
            <textarea 
                name="purpose" 
                id="purpose" 
                rows="3" 
                required 
                placeholder="Jelaskan jenis kegiatan, susunan kepanitiaan, atau tujuan pemakaian aset..."
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-2xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition leading-relaxed"
            >{{ old('purpose') }}</textarea>
        </div>

        <!-- Upload Surat Permohonan (Khusus eksternal / kegiatan formal) -->
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Unggah Surat Permohonan Resmi (Wajib untuk Pihak Luar / Eksternal)
            </label>
            <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl p-4 text-center hover:border-blue-500 transition bg-slate-50 dark:bg-slate-900/40">
                <i class="bi bi-file-earmark-pdf text-2xl text-slate-400"></i>
                <div class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                    <label for="surat_file" class="cursor-pointer text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                        <span>Pilih berkas surat permohonan</span>
                        <input id="surat_file" name="surat_file" type="file" accept=".pdf,.jpg,.png" class="sr-only" onchange="showFileName(this)">
                    </label>
                </div>
                <p id="fileNameDisplay" class="text-[11px] text-slate-400 mt-1 font-mono">Format: PDF, PNG, JPG (Maks. 5 MB)</p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-3">
            <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-blue-600 hover:bg-blue-500 font-bold text-sm text-white shadow-xl shadow-blue-600/30 transition flex items-center justify-center space-x-2">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Kirim Permohonan Reservasi</span>
            </button>
        </div>
    </form>

</div>

<script>
    function showFileName(input) {
        const display = document.getElementById('fileNameDisplay');
        if (input.files && input.files[0]) {
            display.innerText = 'Surat dipilih: ' + input.files[0].name;
            display.classList.add('text-emerald-500', 'font-bold');
        }
    }
</script>
@endsection
