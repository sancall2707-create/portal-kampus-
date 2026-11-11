@extends('layouts.app')

@section('title', 'Ajukan Perizinan — SIAP PL Deltamas')

@section('content')
<div class="w-full max-w-2xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8 space-y-6">

    <div class="space-y-1">
        <a href="{{ route('leaves.index') }}" class="text-xs text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1 transition mb-2">
            <i class="bi bi-arrow-left"></i>
            <span>Batal & Kembali ke Daftar Izin</span>
        </a>
        <h1 class="font-display text-2xl sm:text-3xl font-semibold text-slate-900 dark:text-white tracking-tight">Pengajuan Izin / Sakit / Cuti</h1>
        <p class="text-xs text-slate-600 dark:text-slate-300">Pengajuan diproses berjenjang secara digital dan otomatis terekam ke rekap kehadiran kampus.</p>
    </div>

    <!-- Alert Errors -->
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <i class="bi bi-exclamation-triangle-fill text-rose-600 dark:text-rose-400"></i>
                <span>Pengajuan Gagal</span>
            </div>
            @foreach ($errors->all() as $err)
                <p>- {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <!-- Form Card (LV-01 s.d. LV-03) -->
    <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 space-y-5 shadow-sm dark:shadow-2xl transition-colors">
        @csrf

        <!-- Jenis Izin & Unit -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="leave_type_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Jenis Ketidakhadiran (LV-01)
                </label>
                <select name="leave_type_id" id="leave_type_id" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($leaveTypes as $lt)
                        <option value="{{ $lt->id }}" {{ old('leave_type_id') == $lt->id ? 'selected' : '' }}>
                            {{ $lt->name }} {{ $lt->requires_attachment ? '(Wajib Bukti Dokter)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="unit_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Unit Kerja Saat Pengajuan
                </label>
                <select name="unit_id" id="unit_id" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($userUnits as $u)
                        <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>
                            Unit {{ $u->code }} ({{ $u->name }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Rentang Waktu (Mulai & Selesai) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Tanggal Mulai
                </label>
                <input 
                    type="date" 
                    name="start_date" 
                    id="start_date" 
                    value="{{ old('start_date', now()->toDateString()) }}" 
                    required 
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                >
            </div>
            <div>
                <label for="end_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Tanggal Selesai
                </label>
                <input 
                    type="date" 
                    name="end_date" 
                    id="end_date" 
                    value="{{ old('end_date', now()->toDateString()) }}" 
                    required 
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                >
            </div>
        </div>

        <!-- Alasan Izin -->
        <div>
            <label for="reason" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Alasan Ketidakhadiran
            </label>
            <textarea 
                name="reason" 
                id="reason" 
                rows="3" 
                required 
                placeholder="Jelaskan keperluan atau kondisi sakit secara jelas..."
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-2xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition leading-relaxed"
            >{{ old('reason') }}</textarea>
        </div>

        <!-- Rencana KBM / Titipan Tugas (LV-03) -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="substitute_plan" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Rencana KBM / Titipan Tugas Guru Pengganti (LV-03)
                </label>
                <span class="text-[11px] text-slate-600 dark:text-slate-400">Khusus Tenaga Pendidik</span>
            </div>
            <textarea 
                name="substitute_plan" 
                id="substitute_plan" 
                rows="3" 
                placeholder="Rincian materi, tugas siswa, atau instruksi kerja untuk guru inval yang bertugas..."
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-2xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition leading-relaxed"
            >{{ old('substitute_plan') }}</textarea>
        </div>

        <!-- Unggah Bukti / Surat Dokter (LV-02) -->
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Unggah Bukti Pendukung (Surat Dokter / Undangan Dinas) (LV-02)
            </label>
            <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl p-4 text-center hover:border-blue-500 transition bg-slate-50 dark:bg-slate-900/40">
                <i class="bi bi-cloud-arrow-up text-2xl text-slate-600 dark:text-slate-400"></i>
                <div class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                    <label for="attachment" class="cursor-pointer text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                        <span>Pilih berkas dari perangkat</span>
                        <input id="attachment" name="attachment" type="file" accept=".jpg,.jpeg,.png,.pdf" class="sr-only" onchange="showFileName(this)">
                    </label>
                </div>
                <p id="fileNameDisplay" class="text-[11px] text-slate-600 dark:text-slate-400 mt-1 font-mono">Format: PDF, PNG, JPG (Maks. 5 MB)</p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-3">
            <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-blue-600 hover:bg-blue-500 font-bold text-sm text-white shadow-xl shadow-blue-600/30 transition flex items-center justify-center space-x-2">
                <i class="bi bi-send-fill"></i>
                <span>Kirim Pengajuan Izin</span>
            </button>
        </div>
    </form>

</div>

<script>
    function showFileName(input) {
        const display = document.getElementById('fileNameDisplay');
        if (input.files && input.files[0]) {
            display.innerText = 'Berkas dipilih: ' + input.files[0].name;
            display.classList.add('text-emerald-500', 'font-bold');
        }
    }
</script>
@endsection
