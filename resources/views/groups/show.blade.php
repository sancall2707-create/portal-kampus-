@extends('layouts.app')

@section('title', $group->name.' — Grup & Kepanitiaan')

@section('content')
<div class="w-full space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

    {{-- Navigasi balik --}}
    <a href="{{ route('groups.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">
        <i class="bi bi-chevron-left text-[10px]"></i>
        <span>Kembali ke Daftar Grup</span>
    </a>

    {{-- Notifikasi --}}
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

    {{-- Identitas grup --}}
    @php
        $statusMap = [
            'pending'  => ['Menunggu Persetujuan', 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-500/15 dark:text-amber-300 dark:border-amber-500/30', 'bi-hourglass-split'],
            'active'   => ['Aktif', 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-500/15 dark:text-emerald-300 dark:border-emerald-500/30', 'bi-check-circle-fill'],
            'rejected' => ['Ditolak', 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-500/15 dark:text-rose-300 dark:border-rose-500/30', 'bi-x-circle-fill'],
            'archived' => ['Diarsipkan', 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-white/10 dark:text-slate-300 dark:border-white/15', 'bi-archive-fill'],
        ];
        [$sLabel, $sCls, $sIcon] = $statusMap[$group->status] ?? $statusMap['archived'];
        $catLabels = ['kepanitiaan' => 'Kepanitiaan Acara', 'bidang_kerja' => 'Bidang Kerja', 'proyek' => 'Proyek', 'lainnya' => 'Lainnya'];
    @endphp

    <section class="portal-card p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0 flex-1">
                <p class="label-caps">{{ $catLabels[$group->category] ?? ucfirst($group->category) }}</p>
                <h2 class="mt-1 break-words font-display text-2xl text-slate-900 dark:text-white sm:text-3xl">{{ $group->name }}</h2>
                <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                    {{ $group->unit_code ? 'Unit '.$group->unit_code.' — '.$group->unit_name : 'Skala Kampus (Lintas Unit)' }}
                </p>
            </div>
            <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-wide {{ $sCls }}">
                <i class="bi {{ $sIcon }}"></i>{{ $sLabel }}
            </span>
        </div>

        @if($group->description)
            <p class="mt-4 border-t border-slate-100 pt-4 text-sm leading-relaxed text-slate-700 dark:border-white/10 dark:text-slate-300">{{ $group->description }}</p>
        @endif

        <dl class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 text-xs dark:border-white/10 sm:grid-cols-4">
            <div>
                <dt class="label-caps">Pengaju</dt>
                <dd class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $group->requester_name }}</dd>
            </div>
            <div>
                <dt class="label-caps">Diajukan</dt>
                <dd class="mt-1 font-mono text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($group->created_at)->translatedFormat('d M Y') }}</dd>
            </div>
            <div>
                <dt class="label-caps">Validator</dt>
                <dd class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $group->reviewer_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="label-caps">Jumlah Anggota</dt>
                <dd class="mt-1 font-mono font-semibold text-slate-900 dark:text-white">{{ $members->count() }} orang</dd>
            </div>
        </dl>

        @if($group->status === 'rejected' && $group->reject_reason)
            <p class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-xs text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                <span class="font-bold">Alasan penolakan:</span> {{ $group->reject_reason }}
            </p>
        @endif
    </section>

    {{-- Panel validasi untuk Kepala Unit / Koordinator --}}
    @if($canReview)
        <section class="portal-card p-5">
            <h3 class="label-caps">Validasi Pengajuan Grup</h3>
            <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                Setujui agar grup aktif dan pengaju dapat mengelola anggota, atau tolak dengan alasan yang jelas.
            </p>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <form action="{{ route('groups.review', $group->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="decision" value="approve">
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-600">
                        <i class="bi bi-check2-circle"></i>
                        <span>Setujui Pembentukan Grup</span>
                    </button>
                </form>

                <form action="{{ route('groups.review', $group->id) }}" method="POST" class="space-y-2">
                    @csrf
                    <input type="hidden" name="decision" value="reject">
                    <input type="text" name="reject_reason" required maxlength="300"
                        placeholder="Alasan penolakan (wajib)"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-rose-700 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-rose-600">
                        <i class="bi bi-x-circle"></i>
                        <span>Tolak Pengajuan</span>
                    </button>
                </form>
            </div>
        </section>
    @endif

    {{-- Daftar anggota --}}
    <section class="portal-card overflow-hidden">
        <header class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="font-display text-base text-slate-900 dark:text-white">Anggota Grup</h3>
                <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">Pengelola dapat menambah atau mengeluarkan anggota.</p>
            </div>
            @if($canManage && $candidates->isNotEmpty())
                <button type="button" onclick="document.getElementById('modalMember').classList.remove('hidden')"
                    class="flex shrink-0 items-center gap-2 rounded-xl bg-blue-700 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-blue-600">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Tambah Anggota</span>
                </button>
            @endif
        </header>

        {{-- Kartu anggota di mobile --}}
        <div class="divide-y divide-slate-100 dark:divide-white/10 sm:hidden">
            @forelse($members as $m)
                <div class="flex items-start gap-3 p-4">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-slate-100 text-xs font-bold text-blue-800 dark:bg-white/10 dark:text-blue-300">
                        {{ mb_substr($m->name, 0, 2) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="break-words text-sm font-semibold text-slate-900 dark:text-white">{{ $m->name }}</p>
                        <p class="mt-0.5 font-mono text-[11px] text-slate-600 dark:text-slate-400">{{ $m->nip ?? '—' }} &middot; {{ $m->unit_codes ?? 'Kampus' }}</p>
                        <div class="mt-1.5 flex items-center gap-2">
                            @if($m->member_role === 'admin')
                                <span class="inline-flex items-center gap-1 whitespace-nowrap rounded-full bg-gold-500/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-gold-700 dark:text-gold-300">
                                    <i class="bi bi-person-gear"></i> Pengelola
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 whitespace-nowrap rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-700 dark:bg-white/10 dark:text-slate-300">
                                    Anggota
                                </span>
                            @endif
                            @if($canManage && (int) $m->user_id !== (int) $group->requested_by)
                                <form action="{{ route('groups.remove_member', [$group->id, $m->membership_id]) }}" method="POST" class="ml-auto"
                                    onsubmit="return confirm('Keluarkan {{ $m->name }} dari grup ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-300 px-2 py-1 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10">
                                        <i class="bi bi-person-dash"></i> Keluarkan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="p-8 text-center text-xs text-slate-600 dark:text-slate-400">Belum ada anggota.</p>
            @endforelse
        </div>

        {{-- Tabel anggota di desktop --}}
        <div class="hidden overflow-x-auto sm:block">
            <table class="w-full border-collapse text-left text-xs">
                <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-300">
                    <tr>
                        <th class="px-5 py-3">Nama Pegawai</th>
                        <th class="px-5 py-3">NIP</th>
                        <th class="px-5 py-3">Unit</th>
                        <th class="px-5 py-3">Peran dalam Grup</th>
                        <th class="px-5 py-3">Bergabung</th>
                        @if($canManage)<th class="px-5 py-3 text-right">Aksi</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 dark:divide-white/10 dark:text-slate-300">
                    @forelse($members as $m)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-white/5">
                            <td class="px-5 py-3">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $m->name }}</div>
                                <div class="text-[11px] text-slate-600 dark:text-slate-400">{{ $m->job_title ?? 'Pegawai' }}</div>
                            </td>
                            <td class="px-5 py-3 font-mono">{{ $m->nip ?? '—' }}</td>
                            <td class="px-5 py-3 font-mono">{{ $m->unit_codes ?? 'Kampus' }}</td>
                            <td class="px-5 py-3">
                                @if($m->member_role === 'admin')
                                    <span class="inline-flex items-center gap-1 whitespace-nowrap rounded-full bg-gold-500/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-gold-700 dark:text-gold-300">
                                        <i class="bi bi-person-gear"></i> Pengelola
                                    </span>
                                @else
                                    <span class="inline-flex whitespace-nowrap rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-700 dark:bg-white/10 dark:text-slate-300">Anggota</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-mono text-[11px]">{{ \Carbon\Carbon::parse($m->joined_at)->translatedFormat('d M Y') }}</td>
                            @if($canManage)
                                <td class="px-5 py-3 text-right">
                                    @if((int) $m->user_id !== (int) $group->requested_by)
                                        <form action="{{ route('groups.remove_member', [$group->id, $m->membership_id]) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Keluarkan {{ $m->name }} dari grup ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-300 px-2.5 py-1 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10">
                                                <i class="bi bi-person-dash"></i> Keluarkan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-slate-600 dark:text-slate-300">Pengaju grup</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 6 : 5 }}" class="px-5 py-12 text-center">
                                <i class="bi bi-person-x text-2xl text-slate-400"></i>
                                <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Belum ada anggota terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

{{-- Modal Tambah Anggota --}}
@if($canManage && $candidates->isNotEmpty())
<div id="modalMember" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm sm:flex">
    <div class="w-full max-w-lg space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-white/15 dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-white/10">
            <h3 class="font-display text-sm font-bold text-slate-900 dark:text-white">Tambah Anggota Grup</h3>
            <button type="button" onclick="document.getElementById('modalMember').classList.add('hidden')"
                class="grid h-8 w-8 place-items-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('groups.add_member', $group->id) }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Peran dalam Grup</label>
                <select name="member_role" required
                    class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                    <option value="member">Anggota</option>
                    <option value="admin">Pengelola (dapat menambah anggota)</option>
                </select>
            </div>

            <div>
                <div class="mb-1 flex items-center justify-between">
                    <label class="block font-bold text-slate-700 dark:text-slate-300">Pilih Pegawai (maks. 50)</label>
                    <span id="memberCount" class="font-mono text-[11px] text-slate-600 dark:text-slate-400">0 dipilih</span>
                </div>
                <input type="text" id="memberSearch" onkeyup="filterCandidates(this.value)"
                    placeholder="Cari nama atau NIP pegawai"
                    class="mb-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">

                <div class="max-h-56 space-y-1 overflow-y-auto rounded-xl border border-slate-200 p-2 dark:border-white/10">
                    @foreach($candidates as $c)
                        <label class="candidate-row flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-2 transition hover:bg-slate-50 dark:hover:bg-white/5"
                               data-search="{{ strtolower($c->name.' '.$c->nip) }}">
                            <input type="checkbox" name="user_ids[]" value="{{ $c->id }}" onchange="updateMemberCount()"
                                class="h-4 w-4 shrink-0 rounded border-slate-400 text-blue-700 focus:ring-blue-600">
                            <span class="min-w-0 flex-1">
                                <span class="block truncate font-semibold text-slate-900 dark:text-white">{{ $c->name }}</span>
                                <span class="block truncate font-mono text-[10px] text-slate-600 dark:text-slate-400">
                                    {{ $c->nip ?? '—' }} &middot; {{ $c->unit_codes ?? 'Kampus' }} &middot; {{ $c->job_title ?? 'Pegawai' }}
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalMember').classList.add('hidden')"
                    class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-700 transition hover:bg-slate-200 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/15">Batal</button>
                <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 font-bold text-white transition hover:bg-blue-600">Tambahkan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateMemberCount() {
        const n = document.querySelectorAll('input[name="user_ids[]"]:checked').length;
        document.getElementById('memberCount').textContent = n + ' dipilih';
    }

    function filterCandidates(term) {
        const q = term.toLowerCase().trim();
        document.querySelectorAll('.candidate-row').forEach(function (row) {
            const match = row.dataset.search.indexOf(q) !== -1;
            row.classList.toggle('hidden', q !== '' && !match);
        });
    }
</script>
@endif
@endsection
