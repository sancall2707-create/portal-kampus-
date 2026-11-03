@extends('layouts.app')

@section('title', 'Tata Persuratan & Arsip — SIAP PL Deltamas')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <span>Tata Persuratan & Arsip Digital</span>
                <span class="text-xs font-mono font-normal px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                    {{ $letters->count() }} Surat
                </span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Agenda surat masuk/keluar, penomoran urut otomatis berjenjang, dan penyimpanan arsip digital kampus.
            </p>
        </div>

        @if($user->hasRole(['tata_usaha', 'koordinator']))
            <button type="button" onclick="document.getElementById('modalLetter').classList.remove('hidden')" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                <i class="bi bi-file-earmark-plus"></i>
                <span>Registrasi Surat Baru</span>
            </button>
        @endif
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter Pills (Surat Masuk vs Keluar) -->
    <div class="flex items-center space-x-2 text-xs overflow-x-auto pb-1">
        <a href="{{ route('letters.index', ['direction' => 'all']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $direction === 'all' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Semua Surat
        </a>
        <a href="{{ route('letters.index', ['direction' => 'out']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $direction === 'out' ? 'bg-emerald-600 border-emerald-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Surat Keluar (Penomoran Otomatis)
        </a>
        <a href="{{ route('letters.index', ['direction' => 'in']) }}" 
           class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $direction === 'in' ? 'bg-amber-600 border-amber-500 text-white shadow' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
            Surat Masuk (Eksternal/Dinas)
        </a>
    </div>

    <!-- Letters Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-850 shadow-sm dark:shadow-xl backdrop-blur transition-colors">
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700 font-semibold">
                <tr>
                    <th class="py-3 px-4">Nomor Surat</th>
                    <th class="py-3 px-4">Tanggal</th>
                    <th class="py-3 px-4">Arah / Kategori</th>
                    <th class="py-3 px-4">Perihal & Tujuan/Pengirim</th>
                    <th class="py-3 px-4">Unit</th>
                    <th class="py-3 px-4">Klasifikasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                @forelse($letters as $let)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                        <td class="py-3 px-4 font-mono font-bold text-blue-600 dark:text-blue-400">
                            {{ $let->letter_number }}
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-500 dark:text-slate-400">
                            {{ \Carbon\Carbon::parse($let->letter_date)->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-3 px-4">
                            @if($let->direction === 'out')
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-300">
                                    Keluar
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-300">
                                    Masuk
                                </span>
                            @endif
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $let->category_name }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $let->subject }}</div>
                            <div class="text-[11px] text-slate-500">Pihak: {{ $let->party_name }}</div>
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-700 dark:text-slate-300">
                            Unit {{ $let->unit_code }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $let->classification === 'penting' ? 'bg-amber-100 text-amber-700 border border-amber-300' : ($let->classification === 'rahasia' ? 'bg-rose-100 text-rose-700 border border-rose-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300') }}">
                                {{ $let->classification }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i class="bi bi-folder-x text-3xl"></i>
                            <div class="mt-2 text-xs font-semibold">Belum ada arsip surat yang terdaftar.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- Modal Form Registrasi Surat Baru -->
<div id="modalLetter" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-lg space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">Registrasi Surat Masuk / Keluar</h3>
            <button type="button" onclick="document.getElementById('modalLetter').classList.add('hidden')" class="text-slate-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="{{ route('letters.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Arah Surat</label>
                    <select name="direction" id="directionSelect" onchange="toggleDirection(this.value)" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        <option value="out">Surat Keluar (Auto Nomor)</option>
                        <option value="in">Surat Masuk (Eksternal)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Unit Pengelola</label>
                    <select name="unit_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">Unit {{ $u->code }} ({{ $u->name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori Surat</label>
                    <select name="category_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Surat</label>
                    <input type="date" name="letter_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono">
                </div>
            </div>

            <div id="extNumberContainer" class="hidden">
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Asli Surat Masuk</label>
                <input type="text" name="external_letter_number" placeholder="Contoh: 045/YPL/IX/2026" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Perihal / Hal Surat</label>
                <input type="text" name="subject" required placeholder="Contoh: Undangan Rapat Koordinasi Awal Tahun Ajaran" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tujuan / Pengirim</label>
                    <input type="text" name="party_name" required placeholder="Contoh: Seluruh Orang Tua Murid / Dinas Pendidikan" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Klasifikasi</label>
                    <select name="classification" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        <option value="biasa">Biasa / Umum</option>
                        <option value="penting">Penting</option>
                        <option value="rahasia">Rahasia</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Unggah Berkas PDF (Opsional)</label>
                <input type="file" name="letter_file" accept=".pdf,.jpg,.png" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-400">
            </div>

            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalLetter').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold">Simpan Register Surat</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleDirection(val) {
        const cont = document.getElementById('extNumberContainer');
        if (val === 'in') {
            cont.classList.remove('hidden');
        } else {
            cont.classList.add('hidden');
        }
    }
</script>
@endsection
