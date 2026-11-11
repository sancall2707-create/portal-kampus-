@extends('layouts.app')

@section('title', 'Delegasi Wewenang — Panel Koordinator')

@section('content')
<div class="w-full space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="label-caps">Pusat Kendali</p>
            <h2 class="mt-1 font-display text-2xl text-slate-900 dark:text-white sm:text-3xl">Delegasi Wewenang (Plt)</h2>
            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                Alihkan hak persetujuan sementara agar birokrasi tidak terhambat saat pimpinan cuti, sakit, atau dinas luar.
            </p>
        </div>
        <button type="button" onclick="document.getElementById('modalDelegation').classList.remove('hidden')"
            class="flex shrink-0 items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-700/25 transition hover:bg-blue-600">
            <i class="bi bi-person-fill-up"></i>
            <span>Terbitkan Delegasi</span>
        </button>
    </div>

    @include('admin.partials.nav', ['active' => 'delegations'])

    @if(session('success'))
        <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
            <i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            <i class="bi bi-exclamation-triangle-fill"></i><span>{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            <ul class="ml-5 list-disc space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @php
        $scopeLabels = [
            'approval_leave'        => 'Persetujuan Perizinan',
            'approval_facility'     => 'Persetujuan Fasilitas',
            'academic_assignment'   => 'Pembagian Tugas Akademik',
            'academic_monitoring'   => 'Pemantauan Akademik',
            'all'                   => 'Seluruh Wewenang',
        ];
    @endphp

    {{-- Kartu delegasi di mobile --}}
    <div class="space-y-3 sm:hidden">
        @forelse($delegations as $d)
            @php
                $isRunning = $d->is_active
                    && \Carbon\Carbon::parse($d->start_at)->isPast()
                    && \Carbon\Carbon::parse($d->end_at)->isFuture();
            @endphp
            <article class="portal-card p-4">
                <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-3 dark:border-white/10">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] text-slate-600 dark:text-slate-400">Dari</p>
                        <p class="break-words text-sm font-semibold text-slate-900 dark:text-white">{{ $d->delegator_name }}</p>
                        <p class="mt-1.5 text-[11px] text-slate-600 dark:text-slate-400">Kepada</p>
                        <p class="break-words text-sm font-semibold text-blue-800 dark:text-blue-300">{{ $d->delegate_name }}</p>
                    </div>
                    <span class="shrink-0 whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $isRunning ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300' : ($d->is_active ? 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300') }}">
                        {{ $isRunning ? 'Berjalan' : ($d->is_active ? 'Terjadwal' : 'Dicabut') }}
                    </span>
                </div>
                <dl class="mt-3 space-y-2 text-[11px]">
                    <div><dt class="label-caps">Cakupan</dt><dd class="mt-0.5 font-medium text-slate-800 dark:text-slate-200">{{ $scopeLabels[$d->scope] ?? $d->scope }} &middot; {{ $d->unit_code ? 'Unit '.$d->unit_code : 'Kampus' }}</dd></div>
                    <div><dt class="label-caps">Periode</dt><dd class="mt-0.5 font-mono text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($d->start_at)->format('d/m/y') }} &ndash; {{ \Carbon\Carbon::parse($d->end_at)->format('d/m/y') }}</dd></div>
                    <div><dt class="label-caps">Alasan</dt><dd class="mt-0.5 text-slate-800 dark:text-slate-200">{{ $d->reason }}</dd></div>
                </dl>
                @if($d->is_active)
                    <form action="{{ route('admin.delegations.revoke', $d->id) }}" method="POST" class="mt-3 border-t border-slate-100 pt-3 dark:border-white/10"
                        onsubmit="return confirm('Cabut delegasi wewenang ini?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-300 px-2.5 py-1 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10">
                            <i class="bi bi-x-circle"></i><span>Cabut Delegasi</span>
                        </button>
                    </form>
                @endif
            </article>
        @empty
            <div class="portal-card p-10 text-center">
                <i class="bi bi-person-fill-slash text-2xl text-slate-400"></i>
                <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Belum ada delegasi wewenang.</p>
            </div>
        @endforelse
    </div>

    {{-- Tabel delegasi di desktop --}}
    <div class="portal-card hidden overflow-x-auto sm:block">
        <table class="w-full border-collapse text-left text-xs">
            <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-300">
                <tr>
                    <th class="px-4 py-3">Pemberi Wewenang</th>
                    <th class="px-4 py-3">Penerima (Plt)</th>
                    <th class="px-4 py-3">Cakupan</th>
                    <th class="px-4 py-3">Periode</th>
                    <th class="px-4 py-3">Alasan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 dark:divide-white/10 dark:text-slate-300">
                @forelse($delegations as $d)
                    @php
                        $isRunning = $d->is_active
                            && \Carbon\Carbon::parse($d->start_at)->isPast()
                            && \Carbon\Carbon::parse($d->end_at)->isFuture();
                    @endphp
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $d->delegator_name }}</div>
                            <div class="font-mono text-[11px] text-slate-600 dark:text-slate-400">{{ $d->delegator_nip }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-blue-800 dark:text-blue-300">{{ $d->delegate_name }}</div>
                            <div class="font-mono text-[11px] text-slate-600 dark:text-slate-400">{{ $d->delegate_nip }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="whitespace-nowrap rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-700 dark:bg-white/10 dark:text-slate-200">
                                {{ $scopeLabels[$d->scope] ?? $d->scope }}
                            </span>
                            <div class="mt-0.5 font-mono text-[11px] text-slate-600 dark:text-slate-400">{{ $d->unit_code ? 'Unit '.$d->unit_code : 'Kampus' }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono text-[11px]">
                            {{ \Carbon\Carbon::parse($d->start_at)->format('d/m/y') }}<br>
                            <span class="text-slate-600 dark:text-slate-400">s.d. {{ \Carbon\Carbon::parse($d->end_at)->format('d/m/y') }}</span>
                        </td>
                        <td class="max-w-[14rem] px-4 py-3">
                            <span class="line-clamp-2">{{ $d->reason }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $isRunning ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300' : ($d->is_active ? 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300') }}">
                                {{ $isRunning ? 'Berjalan' : ($d->is_active ? 'Terjadwal' : 'Dicabut') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($d->is_active)
                                <form action="{{ route('admin.delegations.revoke', $d->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Cabut delegasi wewenang ini?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-300 px-2.5 py-1 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10">
                                        <i class="bi bi-x-circle"></i><span>Cabut</span>
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] text-slate-500">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <i class="bi bi-person-fill-slash text-2xl text-slate-400"></i>
                            <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Belum ada delegasi wewenang diterbitkan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Terbitkan Delegasi --}}
<div id="modalDelegation" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm sm:flex">
    <div class="max-h-[90vh] w-full max-w-lg space-y-4 overflow-y-auto rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-white/15 dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-white/10">
            <h3 class="font-display text-sm font-bold text-slate-900 dark:text-white">Terbitkan Delegasi Wewenang</h3>
            <button type="button" onclick="document.getElementById('modalDelegation').classList.add('hidden')"
                class="grid h-8 w-8 place-items-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.delegations.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Pemberi Wewenang (pimpinan yang berhalangan)</label>
                <select name="delegator_id" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                    @foreach($staff as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nip }}) &mdash; {{ $s->unit_codes ?? 'Kampus' }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Penerima Delegasi (Plt / Wakil)</label>
                <select name="delegate_id" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                    @foreach($staff as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nip }}) &mdash; {{ $s->unit_codes ?? 'Kampus' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Cakupan Wewenang</label>
                    <select name="scope" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                        <option value="approval_leave">Persetujuan Perizinan</option>
                        <option value="approval_facility">Persetujuan Fasilitas</option>
                        <option value="academic_assignment">Pembagian Tugas Akademik</option>
                        <option value="academic_monitoring">Pemantauan Akademik</option>
                        <option value="all">Seluruh Wewenang</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Unit (opsional)</label>
                    <select name="unit_id" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                        <option value="">Skala Kampus</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">Unit {{ $u->code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Mulai Berlaku</label>
                    <input type="datetime-local" name="start_at" required value="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 font-mono text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                </div>
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Berakhir</label>
                    <input type="datetime-local" name="end_at" required value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 font-mono text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                </div>
            </div>

            <div>
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Alasan Delegasi (wajib untuk audit)</label>
                <input type="text" name="reason" required maxlength="300" placeholder="Contoh: Kepala SMP mengikuti diklat yayasan di Semarang"
                    class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalDelegation').classList.add('hidden')"
                    class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-700 transition hover:bg-slate-200 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/15">Batal</button>
                <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 font-bold text-white transition hover:bg-blue-600">Terbitkan</button>
            </div>
        </form>
    </div>
</div>
@endsection
