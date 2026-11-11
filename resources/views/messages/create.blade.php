@extends('layouts.app')

@section('title', 'Tulis Pesan — SIAP PL Deltamas')

@section('content')
<div class="w-full max-w-3xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8 space-y-6">

    <div class="space-y-1">
        <a href="{{ route('messages.index') }}" class="text-xs text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1 transition mb-2">
            <i class="bi bi-arrow-left"></i>
            <span>Batal & Kembali ke Kotak Pesan</span>
        </a>
        <h1 class="font-display text-2xl sm:text-3xl font-semibold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
            <span>Tulis Pesan Satu Arah</span>
        </h1>
        <p class="text-xs text-slate-600 dark:text-slate-300">
            Dapat dikirim ke 1 hingga maksimal <strong>50 penerima</strong> sekaligus. Setiap penerima menerima pesan secara privat dan tidak dapat melihat penerima lainnya.
        </p>
    </div>

    <!-- Alert Validation Errors -->
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs space-y-1">
            @foreach ($errors->all() as $err)
                <p>- {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('messages.store') }}" method="POST" id="messageForm" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-sm dark:shadow-2xl transition-colors">
        @csrf

        <!-- 1. Pilih Penerima (Multi-Select s.d 50 Orang) -->
        <div class="space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700 pb-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Pilih Pegawai Penerima <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-600 dark:text-slate-300">Pilih satu per satu atau gunakan filter unit di bawah.</span>
                </div>
                <div class="flex items-center gap-2">
                    <span id="counterBadge" class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                        0 / 50 Terpilih
                    </span>
                    <button type="button" onclick="clearAllSelections()" class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-200 underline">
                        Reset
                    </button>
                </div>
            </div>

            <!-- Search Box & Filter Unit Tabs -->
            <div class="space-y-2">
                <div class="relative">
                    <i class="bi bi-search absolute left-3.5 top-2.5 text-slate-600 dark:text-slate-400 text-xs"></i>
                    <input 
                        type="text" 
                        id="searchRecipientInput" 
                        oninput="filterRecipients()" 
                        placeholder="Cari nama atau NIP pegawai..." 
                        class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 text-[11px]">
                    <button type="button" onclick="filterByUnit('all')" class="unit-filter-btn px-2.5 py-1 rounded-lg border font-semibold bg-blue-600 text-white border-blue-600 shrink-0" data-unit="all">
                        Semua Unit
                    </button>
                    @foreach($units as $u)
                        <button type="button" onclick="filterByUnit('{{ $u->code }}')" class="unit-filter-btn px-2.5 py-1 rounded-lg border font-semibold bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-300 dark:border-slate-700 shrink-0" data-unit="{{ $u->code }}">
                            Unit {{ $u->code }}
                        </button>
                    @endforeach
                    <button type="button" onclick="selectAllVisible()" class="ml-auto px-2.5 py-1 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold hover:bg-slate-300 shrink-0 transition text-[10px]">
                        + Pilih Semua yang Tampil
                    </button>
                </div>
            </div>

            <!-- Recipient Checkbox List Container -->
            <div id="recipientsList" class="max-h-56 overflow-y-auto space-y-1.5 p-2 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700">
                @foreach($recipients as $rec)
                    @php
                        $unitCodes = $rec->units->pluck('code')->implode(' ');
                        $unitLabels = $rec->units->isNotEmpty() ? $rec->units->pluck('code')->implode(', ') : 'Kampus';
                    @endphp
                    <label class="recipient-row flex items-center justify-between p-2.5 rounded-xl hover:bg-white dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700 cursor-pointer transition select-none" data-name="{{ strtolower($rec->name) }}" data-nip="{{ strtolower($rec->nip ?? '') }}" data-units="{{ strtolower($unitCodes) }}">
                        <div class="flex items-center space-x-3 min-w-0">
                            <input 
                                type="checkbox" 
                                name="recipient_ids[]" 
                                value="{{ $rec->id }}" 
                                onchange="handleRecipientToggle(this)" 
                                class="recipient-checkbox h-4 w-4 text-blue-600 rounded border-slate-300 dark:border-slate-700 focus:ring-blue-500 shrink-0"
                            >
                            <div class="truncate">
                                <div class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $rec->name }}</div>
                                <div class="text-[10px] text-slate-600 dark:text-slate-300 font-mono truncate">
                                    NIP: {{ $rec->nip ?? '-' }} · {{ $rec->job_title ?? 'Pegawai' }}
                                </div>
                            </div>
                        </div>

                        <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 shrink-0 font-mono">
                            {{ $unitLabels }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- 2. Kategori Pesan -->
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
                <option value="umum" {{ old('category') == 'umum' ? 'selected' : '' }}>Informasi Kedinasan Lainnya</option>
            </select>
        </div>

        <!-- 3. Perihal / Subjek -->
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

        <!-- 4. Isi Pesan -->
        <div>
            <label for="body" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Isi Pesan Satu Arah <span class="text-rose-500">*</span>
            </label>
            <textarea 
                name="body" 
                id="body" 
                rows="6" 
                required 
                placeholder="Tuliskan isi instruksi, arahan, atau memo dinas secara lengkap dan jelas..."
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-2xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition leading-relaxed"
            >{{ old('body') }}</textarea>
        </div>

        <!-- Privacy Assurance Banner -->
        <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-900 dark:text-amber-200 text-xs flex items-start gap-2.5">
            <i class="bi bi-shield-lock-fill text-amber-600 dark:text-amber-400 text-base shrink-0 mt-0.5"></i>
            <div class="space-y-0.5">
                <strong>Prinsip Kerahasiaan & Satu Arah:</strong>
                <p class="text-[11px] leading-relaxed text-amber-800 dark:text-amber-300">
                    Setiap penerima akan menerima pesan ini sebagai salinan pribadi tersendiri. Penerima tidak dapat melihat daftar penerima lainnya. Pesan tidak menyediakan fitur balas chat agar instruksi kedinasan tetap tertib.
                </p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" id="btnSubmit" class="w-full py-4 px-6 rounded-2xl bg-blue-600 hover:bg-blue-500 font-bold text-sm text-white shadow-xl shadow-blue-600/30 transition flex items-center justify-center space-x-2">
                <i class="bi bi-send-check-fill"></i>
                <span id="submitLabel">Kirim Pesan Satu Arah (0 Penerima)</span>
            </button>
        </div>
    </form>

</div>

<script>
    const MAX_RECIPIENTS = 50;
    let currentUnitFilter = 'all';

    function getSelectedCheckboxes() {
        return document.querySelectorAll('.recipient-checkbox:checked');
    }

    function updateCounter() {
        const count = getSelectedCheckboxes().length;
        const badge = document.getElementById('counterBadge');
        const submitLabel = document.getElementById('submitLabel');
        const btnSubmit = document.getElementById('btnSubmit');

        if (badge) {
            badge.innerText = `${count} / ${MAX_RECIPIENTS} Terpilih`;
            if (count > MAX_RECIPIENTS) {
                badge.className = 'px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-rose-100 text-rose-700 border border-rose-300';
            } else if (count > 0) {
                badge.className = 'px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30';
            } else {
                badge.className = 'px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30';
            }
        }

        if (submitLabel) {
            submitLabel.innerText = `Kirim Pesan Satu Arah (${count} Penerima)`;
        }
    }

    function handleRecipientToggle(checkbox) {
        const count = getSelectedCheckboxes().length;
        if (checkbox.checked && count > MAX_RECIPIENTS) {
            checkbox.checked = false;
            alert(`Batas maksimal tercapai: Anda hanya dapat memilih maksimal ${MAX_RECIPIENTS} penerima per sekali kirim.`);
            return;
        }
        updateCounter();
    }

    function clearAllSelections() {
        document.querySelectorAll('.recipient-checkbox').forEach(cb => cb.checked = false);
        updateCounter();
    }

    function selectAllVisible() {
        const visibleRows = Array.from(document.querySelectorAll('.recipient-row')).filter(r => r.style.display !== 'none');
        let currentCount = getSelectedCheckboxes().length;

        for (const row of visibleRows) {
            const cb = row.querySelector('.recipient-checkbox');
            if (cb && !cb.checked) {
                if (currentCount >= MAX_RECIPIENTS) {
                    alert(`Batas maksimal ${MAX_RECIPIENTS} penerima telah terpenuhi.`);
                    break;
                }
                cb.checked = true;
                currentCount++;
            }
        }
        updateCounter();
    }

    function filterRecipients() {
        const query = document.getElementById('searchRecipientInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.recipient-row');

        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const nip = row.getAttribute('data-nip');
            const units = row.getAttribute('data-units');

            const matchesQuery = !query || name.includes(query) || nip.includes(query);
            const matchesUnit = (currentUnitFilter === 'all') || (units && units.includes(currentUnitFilter.toLowerCase()));

            if (matchesQuery && matchesUnit) {
                row.style.display = 'flex';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function filterByUnit(unitCode) {
        currentUnitFilter = unitCode;

        document.querySelectorAll('.unit-filter-btn').forEach(btn => {
            if (btn.getAttribute('data-unit') === unitCode) {
                btn.className = 'unit-filter-btn px-2.5 py-1 rounded-lg border font-semibold bg-blue-600 text-white border-blue-600 shrink-0';
            } else {
                btn.className = 'unit-filter-btn px-2.5 py-1 rounded-lg border font-semibold bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-300 dark:border-slate-700 shrink-0';
            }
        });

        filterRecipients();
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateCounter();
    });
</script>
@endsection
