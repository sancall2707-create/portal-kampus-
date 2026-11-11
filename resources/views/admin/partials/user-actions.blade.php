@php
    // Atribut data dipakai oleh modal dinamis di admin/users.blade.php
    $dataAttrs = 'data-id="'.$u->id.'"'
        .' data-nip="'.e($u->nip).'"'
        .' data-name="'.e($u->name).'"'
        .' data-email="'.e($u->email).'"'
        .' data-role="'.e($u->role_names).'"'
        .' data-units="'.e($u->unit_codes).'"'
        .' data-job="'.e($u->job_title).'"'
        .' data-category="'.e($u->staff_category).'"'
        .' data-position="'.e($u->unit_position).'"';
@endphp

<button type="button" {!! $dataAttrs !!} onclick="openEditUser(this)"
    class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-2 py-1 text-[10px] font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/15 dark:text-slate-200 dark:hover:bg-white/10"
    title="Ubah data akun">
    <i class="bi bi-pencil-square"></i><span>Ubah</span>
</button>

<button type="button" {!! $dataAttrs !!} onclick="openResetPassword(this)"
    class="inline-flex items-center gap-1 rounded-lg border border-amber-300 px-2 py-1 text-[10px] font-bold text-amber-800 transition hover:bg-amber-50 dark:border-amber-500/30 dark:text-amber-300 dark:hover:bg-amber-500/10"
    title="Reset kata sandi">
    <i class="bi bi-key-fill"></i><span>Sandi</span>
</button>

@if($u->status === 'active' && $u->user_type === 'staff')
    <button type="button" {!! $dataAttrs !!} onclick="openImpersonate(this)"
        class="inline-flex items-center gap-1 rounded-lg border border-blue-300 px-2 py-1 text-[10px] font-bold text-blue-800 transition hover:bg-blue-50 dark:border-blue-500/30 dark:text-blue-300 dark:hover:bg-blue-500/10"
        title="Masuk sebagai akun ini">
        <i class="bi bi-person-badge"></i><span>Simulasi</span>
    </button>
@endif

<form action="{{ route('admin.users.toggle_status', $u->id) }}" method="POST" class="inline"
    onsubmit="return confirm('{{ $u->status === 'active' ? 'Nonaktifkan' : 'Aktifkan kembali' }} akun {{ $u->name }}?')">
    @csrf
    <button type="submit"
        class="inline-flex items-center gap-1 rounded-lg border px-2 py-1 text-[10px] font-bold transition {{ $u->status === 'active' ? 'border-rose-300 text-rose-700 hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10' : 'border-emerald-300 text-emerald-700 hover:bg-emerald-50 dark:border-emerald-500/30 dark:text-emerald-300 dark:hover:bg-emerald-500/10' }}"
        title="{{ $u->status === 'active' ? 'Nonaktifkan akun' : 'Aktifkan akun' }}">
        <i class="bi {{ $u->status === 'active' ? 'bi-person-dash-fill' : 'bi-person-check-fill' }}"></i>
        <span>{{ $u->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}</span>
    </button>
</form>
