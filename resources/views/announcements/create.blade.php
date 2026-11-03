<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Siaran Baru — SIAP PL Deltamas</title>
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
                <a href="{{ route('announcements.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 transition">
                    <span>&larr; Batal & Kembali</span>
                </a>
            </div>
            <div class="text-xs font-semibold text-slate-300">
                Form Siaran Resmi
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-2xl mx-auto px-4 py-8 flex-1 w-full space-y-6">

        <div class="space-y-1">
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Kirim Pengumuman Baru</h1>
            <p class="text-xs text-slate-400">Siarkan arahan, agenda darurat, atau reminder resmi satu arah ke target pegawai.</p>
        </div>

        <!-- Alert Validation Errors -->
        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Pengiriman Gagal</span>
                </div>
                @foreach ($errors->all() as $err)
                    <p>• {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <!-- Form Card (COM-04) -->
        <form action="{{ route('announcements.store') }}" method="POST" class="bg-slate-850 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-5 shadow-2xl backdrop-blur">
            @csrf

            <!-- Scope Target -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                    Target Jangkauan Siaran (Scope)
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @if($canCreateCampus)
                        <label class="relative flex items-center p-3.5 rounded-xl border border-slate-700 bg-slate-900/60 cursor-pointer hover:border-blue-500 transition select-none">
                            <input type="radio" name="scope" value="campus" {{ old('scope', 'campus') === 'campus' ? 'checked' : '' }} onchange="toggleUnitSelect(this.value)" class="h-4 w-4 text-blue-600 bg-slate-950 border-slate-700 focus:ring-blue-500">
                            <div class="ml-3">
                                <div class="text-xs font-bold text-white">Seluruh Kampus</div>
                                <div class="text-[11px] text-slate-400">Seluruh pegawai PG-TK s.d. SMA</div>
                            </div>
                        </label>
                    @endif

                    @if($canCreateUnit)
                        <label class="relative flex items-center p-3.5 rounded-xl border border-slate-700 bg-slate-900/60 cursor-pointer hover:border-blue-500 transition select-none">
                            <input type="radio" name="scope" value="unit" {{ (old('scope') === 'unit' || !$canCreateCampus) ? 'checked' : '' }} onchange="toggleUnitSelect(this.value)" class="h-4 w-4 text-blue-600 bg-slate-950 border-slate-700 focus:ring-blue-500">
                            <div class="ml-3">
                                <div class="text-xs font-bold text-white">Unit Sekolah Tertentu</div>
                                <div class="text-[11px] text-slate-400">Hanya staf pada unit terpilih</div>
                            </div>
                        </label>
                    @endif
                </div>
            </div>

            <!-- Unit Dropdown (conditional) -->
            <div id="unitSelectContainer" class="{{ (old('scope', $canCreateCampus ? 'campus' : 'unit') === 'campus') ? 'hidden' : '' }}">
                <label for="unit_id" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                    Pilih Unit Sasaran
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

            <!-- Kategori Siaran & Urgensi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Kategori Pesan
                    </label>
                    <select name="category" id="category" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="new" {{ old('category') === 'new' ? 'selected' : '' }}>Pesan Baru</option>
                        <option value="reminder" {{ old('category') === 'reminder' ? 'selected' : '' }}>Reminder (Pengingat)</option>
                    </select>
                </div>

                <div>
                    <label for="display_days" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                        Durasi Penayangan
                    </label>
                    <select name="display_days" id="display_days" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1" {{ old('display_days') == '1' ? 'selected' : '' }}>1 Hari (24 Jam)</option>
                        <option value="3" {{ old('display_days') == '3' ? 'selected' : '' }}>3 Hari</option>
                        <option value="7" {{ old('display_days', '7') == '7' ? 'selected' : '' }}>7 Hari (1 Pekan)</option>
                        <option value="14" {{ old('display_days') == '14' ? 'selected' : '' }}>14 Hari (2 Pekan)</option>
                        <option value="30" {{ old('display_days') == '30' ? 'selected' : '' }}>30 Hari (1 Bulan)</option>
                    </select>
                </div>
            </div>

            <!-- Isi Pesan (COM-04 - Max Chars Enforcement) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="body" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Isi Pengumuman
                    </label>
                    <span class="text-[11px] font-mono text-slate-400">
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
                    class="w-full px-4 py-3 bg-slate-950/80 border border-slate-700 rounded-2xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition leading-relaxed"
                >{{ old('body') }}</textarea>
                <p class="text-[11px] text-slate-500 mt-1">
                    Aturan sistem: batas maksimal {{ $maxChars }} karakter agar informasi penting tidak bertele-tele dan mudah dipindai di HP.
                </p>
            </div>

            <!-- Urgent Checkbox -->
            <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold text-white">Tandai Sebagai Pesan PENTING (Urgent)</div>
                    <div class="text-[11px] text-slate-400">Pesan akan diberi sorotan visual merah dan diprioritaskan di daftar teratas.</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-3">
                <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 font-bold text-sm text-white shadow-xl shadow-blue-600/30 transition flex items-center justify-center space-x-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span>Kirim & Siarkan Pengumuman</span>
                </button>
            </div>
        </form>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-900/60 py-4 text-center text-xs text-slate-500">
        &copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · SIAP PL Deltamas
    </footer>

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

        // Init char count on load
        window.onload = function() {
            const bodyEl = document.getElementById('body');
            if (bodyEl) {
                updateCharCount(bodyEl.value);
            }
        };
    </script>
</body>
</html>
