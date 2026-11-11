@extends('layouts.app')

@section('title', 'Manajemen Akun Pegawai — Panel Koordinator')

@section('content')
<div class="w-full space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="label-caps">Pusat Kendali</p>
            <h2 class="mt-1 flex flex-wrap items-center gap-2 font-display text-2xl text-slate-900 dark:text-white sm:text-3xl">
                <span>Manajemen Akun Pegawai</span>
                <span class="rounded-full border border-blue-200 bg-blue-100 px-2.5 py-0.5 font-mono text-xs font-normal text-blue-800 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-300">
                    {{ $users->total() }} Akun
                </span>
            </h2>
            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                Tambah akun, atur peran dan penempatan unit, reset kata sandi, serta nonaktifkan akun purna tugas.
            </p>
        </div>
        <button type="button" onclick="document.getElementById('modalCreateUser').classList.remove('hidden')"
            class="flex shrink-0 items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-700/25 transition hover:bg-blue-600">
            <i class="bi bi-person-plus-fill"></i>
            <span>Tambah Akun</span>
        </button>
    </div>

    @include('admin.partials.nav', ['active' => 'users'])

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

    {{-- Filter & pencarian --}}
    <form action="{{ route('admin.users') }}" method="GET" class="portal-card grid gap-3 p-4 text-xs sm:grid-cols-2 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Cari Pegawai</label>
            <input type="text" name="q" value="{{ $search }}" placeholder="Nama, NIP, atau email"
                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">
        </div>
        <div>
            <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Peran</label>
            <select name="role" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                <option value="all">Semua Peran</option>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}" @selected($roleFilter === $r->name)>{{ ucfirst(str_replace('_', ' ', $r->name)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Unit</label>
            <select name="unit" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                <option value="all">Semua Unit</option>
                @foreach($units as $u)
                    <option value="{{ $u->code }}" @selected($unitFilter === $u->code)>Unit {{ $u->code }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <div class="flex-1">
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                    <option value="all" @selected($statusFilter === 'all')>Semua</option>
                    <option value="active" @selected($statusFilter === 'active')>Aktif</option>
                    <option value="suspended" @selected($statusFilter === 'suspended')>Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="grid h-[38px] w-10 shrink-0 place-items-center rounded-xl bg-ink-900 text-white transition hover:bg-ink-800" title="Terapkan Filter">
                <i class="bi bi-funnel-fill"></i>
            </button>
        </div>
    </form>

    {{-- Kartu akun di mobile --}}
    <div class="space-y-3 sm:hidden">
        @forelse($users as $u)
            <article class="portal-card p-4">
                <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-3 dark:border-white/10">
                    <div class="min-w-0 flex-1">
                        <h3 class="break-words text-sm font-semibold text-slate-900 dark:text-white">{{ $u->name }}</h3>
                        <p class="mt-0.5 font-mono text-[11px] text-slate-600 dark:text-slate-400">{{ $u->nip ?? '—' }}</p>
                    </div>
                    <span class="shrink-0 whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $u->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-500/15 dark:text-rose-300' }}">
                        {{ $u->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <dl class="mt-3 grid grid-cols-2 gap-2 text-[11px]">
                    <div><dt class="label-caps">Peran</dt><dd class="mt-0.5 font-medium text-slate-800 dark:text-slate-200">{{ ucfirst(str_replace('_', ' ', $u->role_names ?? '—')) }}</dd></div>
                    <div><dt class="label-caps">Unit</dt><dd class="mt-0.5 font-mono text-slate-800 dark:text-slate-200">{{ $u->unit_codes ?? 'Kampus' }}</dd></div>
                    <div><dt class="label-caps">Jabatan</dt><dd class="mt-0.5 text-slate-800 dark:text-slate-200">{{ $u->job_title ?? '—' }}</dd></div>
                    <div><dt class="label-caps">Login Terakhir</dt><dd class="mt-0.5 font-mono text-slate-800 dark:text-slate-200">{{ $u->last_login_at ? \Carbon\Carbon::parse($u->last_login_at)->format('d/m/y') : 'Belum pernah' }}</dd></div>
                </dl>
                <div class="mt-3 flex flex-wrap gap-2 border-t border-slate-100 pt-3 dark:border-white/10">
                    @include('admin.partials.user-actions', ['u' => $u])
                </div>
            </article>
        @empty
            <div class="portal-card p-10 text-center">
                <i class="bi bi-person-x text-2xl text-slate-400"></i>
                <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Tidak ada akun yang cocok dengan filter.</p>
            </div>
        @endforelse
    </div>

    {{-- Tabel akun di desktop --}}
    <div class="portal-card hidden overflow-x-auto sm:block">
        <table class="w-full border-collapse text-left text-xs">
            <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-300">
                <tr>
                    <th class="px-4 py-3">NIP</th>
                    <th class="px-4 py-3">Nama &amp; Jabatan</th>
                    <th class="px-4 py-3">Peran</th>
                    <th class="px-4 py-3">Unit</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Login Terakhir</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 dark:divide-white/10 dark:text-slate-300">
                @forelse($users as $u)
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-4 py-3 font-mono font-semibold text-blue-800 dark:text-blue-300">{{ $u->nip ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $u->name }}</div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400">{{ $u->job_title ?? 'Pegawai' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="whitespace-nowrap rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-700 dark:bg-white/10 dark:text-slate-200">
                                {{ str_replace('_', ' ', $u->role_names ?? '—') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono">{{ $u->unit_codes ?? 'Kampus' }}</td>
                        <td class="px-4 py-3">
                            <span class="whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $u->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-500/15 dark:text-rose-300' }}">
                                {{ $u->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-[11px]">
                            {{ $u->last_login_at ? \Carbon\Carbon::parse($u->last_login_at)->format('d/m/y H:i') : 'Belum pernah' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                @include('admin.partials.user-actions', ['u' => $u])
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <i class="bi bi-person-x text-2xl text-slate-400"></i>
                            <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Tidak ada akun yang cocok dengan filter.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="text-xs">{{ $users->links() }}</div>
    @endif
</div>

{{-- Modal Tambah Akun --}}
<div id="modalCreateUser" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm sm:flex">
    <div class="max-h-[90vh] w-full max-w-xl space-y-4 overflow-y-auto rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-white/15 dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-white/10">
            <h3 class="font-display text-sm font-bold text-slate-900 dark:text-white">Tambah Akun Pegawai Baru</h3>
            <button type="button" onclick="document.getElementById('modalCreateUser').classList.add('hidden')"
                class="grid h-8 w-8 place-items-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">NIP Pegawai</label>
                    <input type="text" name="nip" required maxlength="30" value="{{ old('nip') }}" placeholder="Contoh: PL2004"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 font-mono text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">
                </div>
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Kata Sandi Awal</label>
                    <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">
                </div>
            </div>

            <div>
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Nama Lengkap &amp; Gelar</label>
                <input type="text" name="name" required maxlength="150" value="{{ old('name') }}" placeholder="Contoh: Maria Goretti Puspita, S.Pd"
                    class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Email (Opsional)</label>
                    <input type="email" name="email" maxlength="150" value="{{ old('email') }}" placeholder="nama@pangudiluhur.sch.id"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">
                </div>
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Nomor WhatsApp</label>
                    <input type="text" name="phone_wa" maxlength="25" value="{{ old('phone_wa') }}" placeholder="08xxxxxxxxxx"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 font-mono text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Peran Sistem (RBAC)</label>
                    <select name="role" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                        @foreach($roles as $r)
                            @if($r->name !== 'umum')
                                <option value="{{ $r->name }}">{{ ucfirst(str_replace('_', ' ', $r->name)) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Kategori Pegawai</label>
                    <select name="staff_category" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                        <option value="guru">Guru / Pendidik</option>
                        <option value="pimpinan">Pimpinan</option>
                        <option value="tata_usaha">Tata Usaha</option>
                        <option value="satpam">Satuan Pengamanan</option>
                        <option value="pelaksana">Tenaga Pelaksana</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Jabatan Struktural di Unit</label>
                    <select name="position" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                        <option value="guru">Guru</option>
                        <option value="kepala_sekolah">Kepala Sekolah</option>
                        <option value="wakil_kepala">Wakil Kepala Sekolah</option>
                        <option value="tata_usaha">Tata Usaha</option>
                        <option value="satpam">Satuan Pengamanan</option>
                        <option value="pelaksana">Tenaga Pelaksana</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Keterangan Jabatan</label>
                    <input type="text" name="job_title" maxlength="120" value="{{ old('job_title') }}" placeholder="Contoh: Guru Matematika SMP"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500">
                </div>
            </div>

            <div>
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Unit Penempatan (dapat lebih dari satu)</label>
                <div class="grid grid-cols-2 gap-2 rounded-xl border border-slate-200 p-3 dark:border-white/10">
                    @foreach($units as $u)
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" name="unit_ids[]" value="{{ $u->id }}"
                                class="h-4 w-4 rounded border-slate-400 text-blue-700 focus:ring-blue-600">
                            <span class="text-slate-800 dark:text-slate-200">Unit {{ $u->code }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-1 text-[10px] text-slate-600 dark:text-slate-400">Unit pertama yang dicentang menjadi penempatan utama.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalCreateUser').classList.add('hidden')"
                    class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-700 transition hover:bg-slate-200 dark:bg-white/10 dark:text-slate-200 dark:hover:bg-white/15">Batal</button>
                <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 font-bold text-white transition hover:bg-blue-600">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal dinamis: Edit, Reset Sandi, Simulasi Akun --}}
<div id="modalAction" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm sm:flex">
    <div class="max-h-[90vh] w-full max-w-xl space-y-4 overflow-y-auto rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-white/15 dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-white/10">
            <h3 id="actionTitle" class="font-display text-sm font-bold text-slate-900 dark:text-white"></h3>
            <button type="button" onclick="closeActionModal()"
                class="grid h-8 w-8 place-items-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="actionBody" class="text-xs"></div>
    </div>
</div>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const ROLE_OPTIONS = @json($roles->where('name', '!=', 'umum')->pluck('name')->values());
    const UNIT_OPTIONS = @json($units->map(fn ($u) => ['id' => $u->id, 'code' => $u->code])->values());

    function titleCase(s) {
        return s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    }

    function openActionModal(title, html) {
        document.getElementById('actionTitle').textContent = title;
        document.getElementById('actionBody').innerHTML = html;
        document.getElementById('modalAction').classList.remove('hidden');
    }

    function closeActionModal() {
        document.getElementById('modalAction').classList.add('hidden');
        document.getElementById('actionBody').innerHTML = '';
    }

    function openEditUser(btn) {
        const d = btn.dataset;
        const inputCls = 'w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white';
        const labelCls = 'mb-1 block font-bold text-slate-700 dark:text-slate-300';
        const currentUnits = (d.units || '').split('&').map(s => s.trim()).filter(Boolean);

        let roleOpts = ROLE_OPTIONS.map(r =>
            `<option value="${r}" ${d.role === r ? 'selected' : ''}>${titleCase(r)}</option>`).join('');

        let unitBoxes = UNIT_OPTIONS.map(u =>
            `<label class="flex cursor-pointer items-center gap-2">
                <input type="checkbox" name="unit_ids[]" value="${u.id}" ${currentUnits.includes(u.code) ? 'checked' : ''}
                    class="h-4 w-4 rounded border-slate-400 text-blue-700 focus:ring-blue-600">
                <span class="text-slate-800 dark:text-slate-200">Unit ${u.code}</span>
            </label>`).join('');

        const cats = ['guru', 'pimpinan', 'tata_usaha', 'satpam', 'pelaksana'];
        let catOpts = cats.map(c =>
            `<option value="${c}" ${d.category === c ? 'selected' : ''}>${titleCase(c)}</option>`).join('');

        const positions = ['guru', 'kepala_sekolah', 'wakil_kepala', 'tata_usaha', 'satpam', 'pelaksana'];
        let posOpts = positions.map(p =>
            `<option value="${p}" ${d.position === p ? 'selected' : ''}>${titleCase(p)}</option>`).join('');

        openActionModal('Ubah Data Akun — ' + d.name, `
            <form action="/admin/users/${d.id}" method="POST" class="space-y-3">
                <input type="hidden" name="_token" value="${CSRF}">
                <input type="hidden" name="_method" value="PUT">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="${labelCls}">NIP</label>
                        <input type="text" name="nip" required value="${d.nip}" class="${inputCls} font-mono"></div>
                    <div><label class="${labelCls}">Email</label>
                        <input type="email" name="email" value="${d.email || ''}" class="${inputCls}"></div>
                </div>
                <div><label class="${labelCls}">Nama Lengkap</label>
                    <input type="text" name="name" required value="${d.name}" class="${inputCls}"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="${labelCls}">Peran Sistem</label>
                        <select name="role" required class="${inputCls}">${roleOpts}</select></div>
                    <div><label class="${labelCls}">Kategori Pegawai</label>
                        <select name="staff_category" required class="${inputCls}">${catOpts}</select></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="${labelCls}">Jabatan Struktural di Unit</label>
                        <select name="position" required class="${inputCls}">${posOpts}</select></div>
                    <div><label class="${labelCls}">Keterangan Jabatan</label>
                        <input type="text" name="job_title" value="${d.job || ''}" class="${inputCls}"></div>
                </div>
                <div><label class="${labelCls}">Nomor WhatsApp</label>
                    <input type="text" name="phone_wa" value="${d.phone || ''}" class="${inputCls} font-mono"></div>
                <div><label class="${labelCls}">Unit Penempatan</label>
                    <div class="grid grid-cols-2 gap-2 rounded-xl border border-slate-200 p-3 dark:border-white/10">${unitBoxes}</div>
                    <p class="mt-1 text-[10px] text-slate-600 dark:text-slate-400">Unit yang dihapus centangnya akan ditutup sebagai mutasi dengan tanggal berakhir hari ini.</p></div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeActionModal()" class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-700 dark:bg-white/10 dark:text-slate-200">Batal</button>
                    <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 font-bold text-white hover:bg-blue-600">Simpan Perubahan</button>
                </div>
            </form>`);
    }

    function openResetPassword(btn) {
        const d = btn.dataset;
        const inputCls = 'w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white';
        openActionModal('Reset Kata Sandi — ' + d.name, `
            <form action="/admin/users/${d.id}/reset-password" method="POST" class="space-y-3">
                <input type="hidden" name="_token" value="${CSRF}">
                <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-[11px] text-amber-900 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Kata sandi lama akan langsung diganti. Sampaikan sandi baru kepada pegawai melalui kanal pribadi.
                </p>
                <div><label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Kata Sandi Baru</label>
                    <input type="password" name="new_password" required minlength="8" placeholder="Minimal 8 karakter" class="${inputCls}"></div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeActionModal()" class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-700 dark:bg-white/10 dark:text-slate-200">Batal</button>
                    <button type="submit" class="rounded-xl bg-amber-700 px-4 py-2 font-bold text-white hover:bg-amber-600">Reset Sandi</button>
                </div>
            </form>`);
    }

    function openImpersonate(btn) {
        const d = btn.dataset;
        const inputCls = 'w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white';
        openActionModal('Simulasi Akun — ' + d.name, `
            <form action="/admin/users/${d.id}/impersonate" method="POST" class="space-y-3">
                <input type="hidden" name="_token" value="${CSRF}">
                <p class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-[11px] text-blue-900 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-300">
                    <i class="bi bi-info-circle-fill"></i>
                    Anda akan masuk dengan tampilan akun ini untuk investigasi kendala teknis. Seluruh aktivitas tercatat di jejak audit.
                </p>
                <div><label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Alasan Simulasi (wajib)</label>
                    <input type="text" name="reason" required maxlength="300" placeholder="Contoh: Menelusuri keluhan menu izin tidak muncul" class="${inputCls}"></div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeActionModal()" class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-700 dark:bg-white/10 dark:text-slate-200">Batal</button>
                    <button type="submit" class="rounded-xl bg-ink-900 px-4 py-2 font-bold text-white hover:bg-ink-800">Mulai Simulasi</button>
                </div>
            </form>`);
    }
</script>
@endsection
