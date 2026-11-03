<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwalkan Agenda Baru — SIAP PL Deltamas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top App Bar -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('agendas.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 transition">
                    <span>&larr; Batal & Kembali</span>
                </a>
            </div>
            <div class="text-xs font-semibold text-slate-300">
                Form Penjadwalan Agenda
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-2xl mx-auto px-4 py-8 flex-1 w-full space-y-6">

        <div class="space-y-1">
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Jadwalkan Agenda Kegiatan</h1>
            <p class="text-xs text-slate-400">Daftarkan agenda rapat, KBM khusus, misa bersama, atau acara kampus/unit.</p>
        </div>

        <!-- Alert Validation Errors -->
        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Penjadwalan Gagal / Jadwal Bentrok</span>
                </div>
                @foreach ($errors->all() as $err)
                    <p>• {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <!-- Form Card (AG-03 & AG-04) -->
        <form action="{{ route('agendas.store') }}" method="POST" class="bg-slate-850 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-5 shadow-2xl backdrop-blur">
            @csrf

            <!-- Kategori Agenda -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                    Kategori Agenda (AG-01)
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @if($user->hasRole(['koordinator', 'kepala_sekolah']))
                        <label class="relative flex items-center p-3 rounded-xl border border-slate-700 bg-slate-900/60 cursor-pointer hover:border-purple-500 transition select-none">
                            <input type="radio" name="category" value="campus" {{ old('category', 'campus') === 'campus' ? 'checked' : '' }} onchange="toggleCategory(this.value)" class="h-4 w-4 text-purple-600 bg-slate-950 border-slate-700 focus:ring-purple-500">
                            <div class="ml-2.5">
                                <div class="text-xs font-bold text-white">Kampus</div>
                                <div class="text-[10px] text-slate-400">Seluruh unit</div>
                            </div>
                        </label>
                    @endif

                    <label class="relative flex items-center p-3 rounded-xl border border-slate-700 bg-slate-900/60 cursor-pointer hover:border-blue-500 transition select-none">
                        <input type="radio" name="category" value="unit" {{ (old('category') === 'unit' || !$user->hasRole(['koordinator', 'kepala_sekolah'])) ? 'checked' : '' }} onchange="toggleCategory(this.value)" class="h-4 w-4 text-blue-600 bg-slate-950 border-slate-700 focus:ring-blue-500">
                        <div class="ml-2.5">
                            <div class="text-xs font-bold text-white">Unit Sekolah</div>
                            <div class="text-[10px] text-slate-400">Internal unit</div>
                        </div>
                    </label>

                    <label class="relative flex items-center p-3 rounded-xl border border-slate-700 bg-slate-900/60 cursor-pointer hover:border-emerald-500 transition select-none">
                        <input type="radio" name="category" value="personal" {{ old('category') === 'personal' ? 'checked' : '' }} onchange="toggleCategory(this.value)" class="h-4 w-4 text-emerald-600 bg-slate-950 border-slate-700 focus:ring-emerald-500">
                        <div class="ml-2.5">
                            <div class="text-xs font-bold text-white">Pribadi</div>
                            <div class="text-[10px] text-slate-400">Hanya saya</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Unit Dropdown (conditional) -->
            <div id="unitSelectContainer" class="{{ (old('category', $user->hasRole(['koordinator', 'kepala_sekolah']) ? 'campus' : 'unit') === 'unit') ? '' : 'hidden' }}">
                <label for="unit_id" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                    Pilih Unit Terkait
                </label>
                <select name="unit_id" id="unit_id" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Unit Sekolah --</option>
                    @foreach($availableUnits as $u)
                        <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>
                            Unit {{ $u->code }} ({{ $u->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Judul Agenda -->
            <div>
                <label for="title" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                    Nama Agenda / Kegiatan
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title') }}" 
                    placeholder="Contoh: Rapat Koordinasi Kurikulum, Misa Bulanan" 
                    required 
                    class="w-full px-4 py-3 bg-slate-950/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                >
            </div>

            <!-- Rentang Waktu (Mulai & Selesai) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_at" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Waktu Mulai
                    </label>
                    <input 
                        type="datetime-local" 
                        name="start_at" 
                        id="start_at" 
                        value="{{ old('start_at') }}" 
                        required 
                        class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                    >
                </div>
                <div>
                    <label for="end_at" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Waktu Selesai
                    </label>
                    <input 
                        type="datetime-local" 
                        name="end_at" 
                        id="end_at" 
                        value="{{ old('end_at') }}" 
                        required 
                        class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                    >
                </div>
            </div>

            <!-- Ruang / Fasilitas Kampus -->
            <div>
                <label for="facility_id" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                    Ruang / Fasilitas yang Dipakai (Opsional)
                </label>
                <select name="facility_id" id="facility_id" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Tanpa Ruang Khusus / Fasilitas Luar --</option>
                    @foreach($facilities as $f)
                        <option value="{{ $f->id }}" {{ old('facility_id') == $f->id ? 'selected' : '' }}>
                            {{ $f->name }} (Kapasitas ~{{ $f->capacity ?? '-' }} orang)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Blokir Peminjaman Checkbox (AG-04) -->
            <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold text-amber-300 flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Kunci & Blokir Reservasi Fasilitas (AG-04)</span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">
                        Jika diaktifkan, sistem akan otomatis menolak seluruh pengajuan reservasi umum/internal yang bentrok di jam yang sama.
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                    <input type="checkbox" name="blocks_booking" value="1" {{ old('blocks_booking', '1') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                </label>
            </div>

            <!-- Rincian Agenda (Max 100 chars) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="description" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Rincian Acara
                    </label>
                    <span class="text-[11px] font-mono text-slate-400">
                        <span id="charCount">0</span> / {{ $maxChars }} Karakter
                    </span>
                </div>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="3" 
                    maxlength="{{ $maxChars }}"
                    placeholder="Catatan tambahan (pakaian, perlengkapan yang perlu dibawa, dll)..."
                    oninput="updateCharCount(this.value)"
                    class="w-full px-4 py-3 bg-slate-950/80 border border-slate-700 rounded-2xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition leading-relaxed"
                >{{ old('description') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-3">
                <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 font-bold text-sm text-white shadow-xl shadow-blue-600/30 transition flex items-center justify-center space-x-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Simpan ke Kalender Agenda</span>
                </button>
            </div>
        </form>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-900/60 py-4 text-center text-xs text-slate-500">
        &copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · SIAP PL Deltamas
    </footer>

    <script>
        function toggleCategory(cat) {
            const container = document.getElementById('unitSelectContainer');
            if (cat === 'unit') {
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
            const descEl = document.getElementById('description');
            if (descEl) {
                updateCharCount(descEl.value);
            }
        };
    </script>
</body>
</html>
