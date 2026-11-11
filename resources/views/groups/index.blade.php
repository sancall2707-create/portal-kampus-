@extends('layouts.app')

@section('title', 'Grup & Kepanitiaan — SIAP PL Deltamas')

@section('content')
<div class="w-full space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="label-caps">Kolaborasi</p>
            <h2 class="mt-1 flex flex-wrap items-center gap-2 font-display text-2xl text-slate-900 dark:text-white sm:text-3xl">
                <span>Grup &amp; Kepanitiaan</span>
                <span class="rounded-full border border-blue-200 bg-blue-100 px-2.5 py-0.5 font-mono text-xs font-normal text-blue-800 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-300">
                    {{ $groups->count() }} Grup
                </span>
                @if($pendingCount > 0 && $user->hasPermissionTo('group.approve'))
                    <span class="rounded-full border border-amber-300 bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                        {{ $pendingCount }} menunggu validasi
                    </span>
                @endif
            </h2>
            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                Pengajuan kepanitiaan melalui persetujuan Kepala Unit, lalu dikelola oleh pengaju sebagai pengelola grup.
            </p>
        </div>

        @if($user->hasPermissionTo('group.request'))
            <button type="button" onclick="document.getElementById('modalGroup').classList.remove('hidden')"
                class="flex shrink-0 items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-700/25 transition hover:bg-blue-600">
                <i class="bi bi-people-fill"></i>
                <span>Ajukan Grup Baru</span>
            </button>
        @endif
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            <p class="mb-1 flex items-center gap-2 font-bold"><i class="bi bi-exclamation-triangle-fill"></i> Periksa kembali data pengajuan:</p>
            <ul class="ml-5 list-disc space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Filter status --}}
    @php
        $filters = [
            'all'      => ['label' => 'Semua', 'cls' => 'bg-blue-700 border-blue-600'],
            'pending'  => ['label' => 'Menunggu Persetujuan', 'cls' => 'bg-amber-600 border-amber-500'],
            'active'   => ['label' => 'Aktif', 'cls' => 'bg-emerald-700 border-emerald-600'],
            'rejected' => ['label' => 'Ditolak', 'cls' => 'bg-rose-700 border-rose-600'],
        ];
    @endphp
    <div class="flex flex-wrap items-center gap-2 text-xs">
        @foreach($filters as $key => $f)
            <a href="{{ route('groups.index', ['status' => $key]) }}"
               class="shrink-0 rounded-lg border px-3.5 py-1.5 font-semibold transition {{ $statusFilter === $key ? $f['cls'].' text-white shadow' : 'border-slate-200 bg-white text-slate-700 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-white' }}">
                {{ $f['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Daftar grup --}}
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($groups as $g)
            @php
                $statusMap = [
                    'pending'  => ['Menunggu Persetujuan', 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-500/15 dark:text-amber-300 dark:border-amber-500/30', 'bi-hourglass-split'],
                    'active'   => ['Aktif', 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-500/15 dark:text-emerald-300 dark:border-emerald-500/30', 'bi-check-circle-fill'],
                    'rejected' => ['Ditolak', 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-500/15 dark:text-rose-300 dark:border-rose-500/30', 'bi-x-circle-fill'],
                    'archived' => ['Diarsipkan', 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-white/10 dark:text-slate-300 dark:border-white/15', 'bi-archive-fill'],
                ];
                [$sLabel, $sCls, $sIcon] = $statusMap[$g->status] ?? $statusMap['archived'];
                $myRole = $myMemberships[$g->id] ?? null;
                $catLabels = [
                    'kepanitiaan'  => 'Kepanitiaan',
                    'bidang_kerja' => 'Bidang Kerja',
                    'proyek'       => 'Proyek',
                    'lainnya'      => 'Lainnya',
                ];
            @endphp
            <article class="portal-card flex flex-col p-4">
                <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-3 dark:border-white/10">
                    <div class="min-w-0 flex-1">
                        <h3 class="break-words font-display text-base font-semibold text-slate-900 dark:text-white">{{ $g->name }}</h3>
                        <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">
                            {{ $catLabels[$g->category] ?? ucfirst($g->category) }}
                            &middot; {{ $g->unit_code ? 'Unit '.$g->unit_code : 'Skala Kampus' }}
                        </p>
                    </div>
                    <span class="inline-flex shrink-0 items-center gap-1 whitespace-nowrap rounded-full border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $sCls }}">
                        <i class="bi {{ $sIcon }}"></i>
                        {{ $sLabel }}
                    </span>
                </div>

                @if($g->description)
                    <p class="mt-3 line-clamp-2 text-xs leading-relaxed text-slate-700 dark:text-slate-300">{{ $g->description }}</p>
                @endif

                @if($g->status === 'rejected' && $g->reject_reason)
                    <p class="mt-3 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-2 text-[11px] text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                        <span class="font-bold">Alasan:</span> {{ $g->reject_reason }}
                    </p>
                @endif

                <dl class="mt-3 grid grid-cols-2 gap-2 text-[11px]">
                    <div>
                        <dt class="label-caps">Pengaju</dt>
                        <dd class="mt-0.5 truncate font-medium text-slate-800 dark:text-slate-200">{{ $g->requester_name }}</dd>
                    </div>
                    <div>
                        <dt class="label-caps">Anggota</dt>
                        <dd class="mt-0.5 font-mono font-semibold text-slate-800 dark:text-slate-200">{{ $g->member_count }} orang</dd>
                    </div>
                </dl>

                <div class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-3 dark:border-white/10">
                    @if($myRole === 'admin')
                        <span class="inline-flex items-center gap-1 rounded-full bg-gold-500/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-gold-700 dark:text-gold-300">
                            <i class="bi bi-person-gear"></i> Pengelola
                        </span>
                    @elseif($myRole === 'member')
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-blue-800 dark:bg-blue-500/15 dark:text-blue-300">
                            <i class="bi bi-person-check"></i> Anggota
                        </span>
                    @endif
                    <a href="{{ route('groups.show', $g->id) }}"
                       class="ml-auto inline-flex items-center gap-1.5 rounded-lg bg-ink-900 px-3 py-1.5 text-[11px] font-bold text-white transition hover:bg-ink-800">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>{{ $g->status === 'pending' && $user->hasPermissionTo('group.approve') ? 'Validasi' : 'Akses' }}</span>
                    </a>
                </div>
            </article>
        @empty
            <div class="portal-card col-span-full p-12 text-center">
                <i class="bi bi-people text-3xl text-slate-400"></i>
                <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Belum ada grup kepanitiaan.</p>
                <p class="mt-1 text-[11px] text-slate-600 dark:text-slate-400">Ajukan pembentukan grup untuk memulai koordinasi kepanitiaan.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Modal Pengajuan Grup Baru --}}
@if($user->hasPermissionTo('group.request'))
<div id="modalGroup" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm sm:flex">
    <div class="w-full max-w-lg space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-white/15 dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-white/10">
            <h3 class="font-display text-sm font-bold text-slate-900 dark:text-white">Ajukan Pembentukan Grup</h3>
            <button type="button" onclick="document.getElementById('modalGroup').classList.add('hidden')"
                class="grid h-8 w-8 place-items-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('groups.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Nama Kepanitiaan / Grup</label>
                <input type="text" name="name" required maxlength="120" value="{{ old('name') }}"
                    placeholder="Contoh: Panitia Natal Kampus 2026"
                    class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Kategori</label>
                    <select name="category" required
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                        <option value="kepanitiaan">Kepanitiaan Acara</option>
                        <option value="bidang_kerja">Bidang Kerja</option>
                        <option value="proyek">Proyek</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Cakupan</label>
                    <select name="scope" id="scopeSelect" onchange="toggleUnitField(this.value)" required
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                        <option value="unit">Skala Unit Sekolah</option>
                        <option value="campus">Skala Kampus (Lintas Unit)</option>
                    </select>
                </div>
            </div>

            <div id="unitFieldContainer">
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Unit Sekolah</label>
                <select name="unit_id"
                    class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                    @foreach($units as $u)
                        <option value="{{ $u->id }}">Unit {{ $u->code }} ({{ $u->name }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Deskripsi / Tujuan Grup</label>
                <textarea name="description" rows="3" maxlength="500"
                    placeholder="Jelaskan singkat tujuan pembentukan grup kepanitiaan ini."
                    class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 leading-relaxed text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">{{ old('description') }}</textarea>
            </div>

            <p class="flex items-start gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-2 text-[11px] text-blue-800 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-300">
                <i class="bi bi-info-circle-fill mt-0.5"></i>
                <span>Pengajuan dikirim ke Kepala Unit untuk divalidasi. Anda otomatis menjadi pengelola grup bila disetujui.</span>
            </p>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalGroup').classList.add('hidden')"
                    class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-700 transition hover:bg-slate-200 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/15">Batal</button>
                <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 font-bold text-white transition hover:bg-blue-600">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleUnitField(scope) {
        const cont = document.getElementById('unitFieldContainer');
        if (scope === 'campus') {
            cont.classList.add('hidden');
        } else {
            cont.classList.remove('hidden');
        }
    }
</script>
@endif
@endsection
