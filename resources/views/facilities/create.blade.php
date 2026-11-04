@extends('layouts.app')

@section('title', 'Ajukan Peminjaman Fasilitas — SIAP PL Deltamas')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 space-y-6">

    <div class="space-y-1">
        <a href="{{ route('facilities.index') }}" class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1 transition mb-2">
            <i class="bi bi-arrow-left"></i>
            <span>Batal & Kembali ke Daftar Fasilitas</span>
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Formulir Cerdas Peminjaman Fasilitas</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400">Pengajuan otomatis divalidasi dengan penguncian atomik basis data dan jeda persiapan 15 menit.</p>
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

    <!-- Smart Form Card (PRD Migrasi 5.2) -->
    <form action="{{ route('facilities.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-sm dark:shadow-2xl transition-colors">
        @csrf

        <!-- 1. Pilih Fasilitas -->
        <div>
            <label for="facility_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Pilih Fasilitas / Kendaraan / Ruangan <span class="text-rose-500">*</span>
            </label>
            <select name="facility_id" id="facility_id" required onchange="handleFacilityChange(this)" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Fasilitas Kampus --</option>
                @foreach($facilities as $f)
                    <option value="{{ $f->id }}" data-category="{{ strtolower($f->category_name) }}" {{ (old('facility_id', $selectedFacilityId) == $f->id) ? 'selected' : '' }}>
                        [{{ strtoupper($f->category_name) }}] {{ $f->name }} (Kapasitas: {{ $f->capacity ? "~{$f->capacity} orang" : '-' }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- 2. Bidang Khusus Kendaraan (Tampil jika kategori kendaraan dipilih) -->
        <div id="vehicleSection" class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/50 space-y-4 hidden">
            <div class="text-xs font-bold text-blue-900 dark:text-blue-300 flex items-center gap-1.5">
                <i class="bi bi-truck-front-fill"></i>
                <span>Informasi Khusus Kendaraan Dinas Operasional</span>
            </div>
            <div>
                <label for="destination" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Tujuan Perjalanan Dinas / Alamat Tujuan
                </label>
                <input 
                    type="text" 
                    name="destination" 
                    id="destination" 
                    value="{{ old('destination') }}" 
                    placeholder="Contoh: Kantor Yayasan PL Semarang / Paroki Cikarang" 
                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="driver_needed" id="driver_needed" value="1" {{ old('driver_needed') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded border-slate-300 dark:border-slate-700">
                <label for="driver_needed" class="text-xs font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                    Butuh Disediakan Supir Resmi Kampus (Dialokasikan oleh Koordinator Sarpras)
                </label>
            </div>
        </div>

        <!-- 3. Bidang Nama Acara (Untuk Ruang / Lapangan / Umum) -->
        <div id="roomSection">
            <label for="event_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Nama Acara / Kegiatan
            </label>
            <input 
                type="text" 
                name="event_name" 
                id="event_name" 
                value="{{ old('event_name') }}" 
                placeholder="Contoh: Rapat Pleno Yayasan / Retret Siswa / Misa Kampus" 
                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <!-- 4. Penanggung Jawab & Kontak Darurat WhatsApp -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="pic_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Nama Penanggung Jawab (PIC) <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="pic_name" 
                    id="pic_name" 
                    value="{{ old('pic_name', $user->name) }}" 
                    required 
                    placeholder="Nama lengkap PIC" 
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <div>
                <label for="pic_phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    No. WhatsApp PIC (Kontak Darurat) <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="pic_phone" 
                    id="pic_phone" 
                    value="{{ old('pic_phone', $user->phone_wa) }}" 
                    required 
                    placeholder="08xxxxxxxxxx" 
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                >
            </div>
        </div>

        <!-- 5. Rentang Waktu Pemakaian -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="start_at" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Waktu Mulai Pemakaian <span class="text-rose-500">*</span>
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
                    Waktu Selesai Pemakaian <span class="text-rose-500">*</span>
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

        <!-- 6. Estimasi Jumlah Peserta -->
        <div>
            <label for="participants" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Estimasi Jumlah Peserta (Orang)
            </label>
            <input 
                type="number" 
                name="participants" 
                id="participants" 
                value="{{ old('participants') }}" 
                placeholder="Contoh: 80" 
                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
            >
        </div>

        <!-- 7. Tujuan Kegiatan & Rincian -->
        <div>
            <label for="purpose" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Tujuan Kegiatan & Rincian Pemakaian <span class="text-rose-500">*</span>
            </label>
            <textarea 
                name="purpose" 
                id="purpose" 
                rows="3" 
                required 
                placeholder="Jelaskan jenis kegiatan, susunan kepanitiaan, atau keperluan peminjaman..."
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-2xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition leading-relaxed"
            >{{ old('purpose') }}</textarea>
        </div>

        <!-- 8. Modul Penambahan Barang Sarpras (PRD Migrasi 5.2) -->
        <div class="space-y-3 border-t border-slate-100 dark:border-slate-700 pt-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-box-seam-fill text-indigo-600 dark:text-indigo-400"></i>
                        <span>Kebutuhan Peralatan Sarpras Tambahan</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Centang dan tentukan jumlah barang yang diperlukan untuk kegiatan ini.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($availableItems as $item)
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
                        <div class="flex items-center space-x-2.5">
                            <input 
                                type="checkbox" 
                                id="item_check_{{ $item->id }}" 
                                onchange="toggleItemQty({{ $item->id }})" 
                                class="h-4 w-4 text-blue-600 rounded border-slate-300 dark:border-slate-700"
                            >
                            <div>
                                <label for="item_check_{{ $item->id }}" class="text-xs font-semibold text-slate-800 dark:text-slate-200 cursor-pointer block">
                                    {{ $item->name }}
                                </label>
                                <span class="text-[10px] text-slate-400 font-mono">Tersedia: {{ $item->total_stock }} unit</span>
                            </div>
                        </div>

                        <div class="w-20">
                            <input 
                                type="number" 
                                name="items[{{ $item->id }}]" 
                                id="item_qty_{{ $item->id }}" 
                                min="1" 
                                max="{{ $item->total_stock }}" 
                                placeholder="Qty" 
                                disabled
                                class="w-full px-2 py-1 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-center font-mono text-slate-900 dark:text-white disabled:opacity-40"
                            >
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 9. Upload Surat Permohonan Resmi -->
        <div class="border-t border-slate-100 dark:border-slate-700 pt-5">
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
        <div class="pt-2">
            <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-blue-600 hover:bg-blue-500 font-bold text-sm text-white shadow-xl shadow-blue-600/30 transition flex items-center justify-center space-x-2">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Kirim Permohonan Reservasi Cerdas</span>
            </button>
        </div>
    </form>

</div>

<script>
    function handleFacilityChange(select) {
        const selected = select.options[select.selectedIndex];
        const category = selected ? selected.getAttribute('data-category') : '';
        const vSec = document.getElementById('vehicleSection');
        if (category === 'kendaraan') {
            vSec.classList.remove('hidden');
        } else {
            vSec.classList.add('hidden');
        }
    }

    function toggleItemQty(id) {
        const chk = document.getElementById('item_check_' + id);
        const qty = document.getElementById('item_qty_' + id);
        if (chk && qty) {
            qty.disabled = !chk.checked;
            if (chk.checked) {
                if (!qty.value) qty.value = 1;
                qty.focus();
            } else {
                qty.value = '';
            }
        }
    }

    function showFileName(input) {
        const display = document.getElementById('fileNameDisplay');
        if (input.files && input.files[0]) {
            display.innerText = 'Surat dipilih: ' + input.files[0].name;
            display.classList.add('text-emerald-500', 'font-bold');
        }
    }

    // Trigger on load for pre-selected option
    document.addEventListener('DOMContentLoaded', () => {
        const sel = document.getElementById('facility_id');
        if (sel) handleFacilityChange(sel);
    });
</script>
@endsection
