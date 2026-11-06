@extends('layouts.app')

@section('title', 'Buat Siaran Baru — SIAP PL Deltamas')

@section('content')
<div class="w-full max-w-2xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8 space-y-6">

    <div class="space-y-1">
        <a href="{{ route('announcements.index') }}" class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1 transition mb-2">
            <i class="bi bi-arrow-left"></i>
            <span>Batal & Kembali ke Papan Siaran</span>
        </a>
        <h1 class="font-display text-2xl sm:text-3xl font-semibold text-slate-900 dark:text-white tracking-tight">Kirim Pengumuman Baru</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400">Siarkan arahan, agenda darurat, atau reminder resmi satu arah ke target pegawai.</p>
    </div>

    <!-- Alert Validation Errors -->
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <i class="bi bi-exclamation-triangle-fill text-rose-600 dark:text-rose-400"></i>
                <span>Pengiriman Gagal</span>
            </div>
            @foreach ($errors->all() as $err)
                <p>- {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <!-- Form Card -->
    <form action="{{ route('announcements.store') }}" method="POST" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 space-y-5 shadow-sm dark:shadow-2xl transition-colors">
        @csrf

        <!-- Scope Target -->
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                Target Jangkauan Siaran (Scope)
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @if($canCreateCampus)
                    <label class="relative flex items-center p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/60 cursor-pointer hover:border-blue-500 transition select-none">
                        <input type="radio" name="scope" value="campus" {{ old('scope', 'campus') === 'campus' ? 'checked' : '' }} onchange="toggleUnitSelect(this.value)" class="h-4 w-4 text-blue-600 bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 focus:ring-blue-500">
                        <div class="ml-3">
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Seluruh Kampus</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Seluruh pegawai PG-TK s.d. SMA</div>
                        </div>
                    </label>
                @endif

                @if($canCreateUnit)
                    <label class="relative flex items-center p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/60 cursor-pointer hover:border-blue-500 transition select-none">
                        <input type="radio" name="scope" value="unit" {{ (old('scope') === 'unit' || !$canCreateCampus) ? 'checked' : '' }} onchange="toggleUnitSelect(this.value)" class="h-4 w-4 text-blue-600 bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 focus:ring-blue-500">
                        <div class="ml-3">
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Unit Sekolah Tertentu</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Hanya staf pada unit terpilih</div>
                        </div>
                    </label>
                @endif
            </div>
        </div>

        <!-- Unit Dropdown (conditional) -->
        <div id="unitSelectContainer" class="{{ (old('scope', $canCreateCampus ? 'campus' : 'unit') === 'campus') ? 'hidden' : '' }}">
            <label for="unit_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Pilih Unit Sasaran
            </label>
            <select name="unit_id" id="unit_id" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Unit Sekolah --</option>
                @foreach($availableUnits as $u)
                    <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>
                        Unit {{ $u->code }} ({{ $u->name }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Kategori Siaran & Durasi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="category" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Kategori Pesan
                </label>
                <select name="category" id="category" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="new" {{ old('category') === 'new' ? 'selected' : '' }}>Pesan Baru</option>
                    <option value="reminder" {{ old('category') === 'reminder' ? 'selected' : '' }}>Reminder (Pengingat)</option>
                </select>
            </div>

            <div>
                <label for="display_days" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Durasi Penayangan
                </label>
                <select name="display_days" id="display_days" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="1" {{ old('display_days') == '1' ? 'selected' : '' }}>1 Hari (24 Jam)</option>
                    <option value="3" {{ old('display_days') == '3' ? 'selected' : '' }}>3 Hari</option>
                    <option value="7" {{ old('display_days', '7') == '7' ? 'selected' : '' }}>7 Hari (1 Pekan)</option>
                    <option value="14" {{ old('display_days') == '14' ? 'selected' : '' }}>14 Hari (2 Pekan)</option>
                    <option value="30" {{ old('display_days') == '30' ? 'selected' : '' }}>30 Hari (1 Bulan)</option>
                </select>
            </div>
        </div>

        <!-- Isi Pesan (Max Chars) -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="body" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Isi Pengumuman
                </label>
                <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400">
                    <span id="charCount">0</span> / {{ $maxChars }} Karakter
                </span>
            </div>
            <textarea 
                name="body" 
                id="body" 
                rows="4" 
                maxlength="{{ $maxChars }}"
                placeholder="Tuliskan pesan instruksi resmi secara singkat, padat, dan jelas..."
                required
                oninput="updateCharCount(this.value)"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-2xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition leading-relaxed"
            >{{ old('body') }}</textarea>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">
                Aturan sistem: batas maksimal {{ $maxChars }} karakter agar informasi penting tidak bertele-tele dan mudah dipindai di HP.
            </p>
        </div>

        <!-- Urgent Checkbox -->
        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <div class="text-xs font-bold text-slate-900 dark:text-white">Tandai Sebagai Pesan PENTING (Urgent)</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400">Pesan akan diberi sorotan visual merah dan diprioritaskan di daftar teratas.</div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent') ? 'checked' : '' }} class="sr-only peer">
                <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-3">
            <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-blue-600 hover:bg-blue-500 font-bold text-sm text-white shadow-xl shadow-blue-600/30 transition flex items-center justify-center space-x-2">
                <i class="bi bi-send-fill"></i>
                <span>Kirim & Siarkan Pengumuman</span>
            </button>
        </div>
    </form>

</div>

<script>
    function toggleUnitSelect(scope) {
        const container = document.getElementById('unitSelectContainer');
        if (scope === 'unit') {
            container.classList.remove('hidden');
            document.getElementById('unit_id').required = true;
        } else {
            container.classList.add('hidden');
            document.getElementById('unit_id').required = false;
        }
    }

    function updateCharCount(text) {
        document.getElementById('charCount').innerText = text.length;
    }

    window.onload = function() {
        const bodyEl = document.getElementById('body');
        if (bodyEl) {
            updateCharCount(bodyEl.value);
        }
    };
</script>
@endsection
