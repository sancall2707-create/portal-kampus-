@extends('layouts.app')

@section('title', 'Data Induk & Pengaturan — Panel Koordinator')

@section('content')
<div class="w-full space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

    <div>
        <p class="label-caps">Pusat Kendali</p>
        <h2 class="mt-1 font-display text-2xl text-slate-900 dark:text-white sm:text-3xl">Data Induk &amp; Pengaturan</h2>
        <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
            Struktur unit sekolah, tahun ajaran aktif, jenis perizinan, dan parameter sistem kampus.
        </p>
    </div>

    @include('admin.partials.nav', ['active' => 'master'])

    @if(session('success'))
        <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
            <i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            <ul class="ml-5 list-disc space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Unit sekolah --}}
    <section class="portal-card overflow-hidden">
        <header class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
            <h3 class="font-display text-base text-slate-900 dark:text-white">Struktur Unit Sekolah</h3>
            <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">Jenjang pendidikan di bawah naungan Kampus Pangudi Luhur Deltamas.</p>
        </header>
        <div class="grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($units as $u)
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-start justify-between gap-2">
                        <span class="font-display text-lg font-semibold text-slate-900 dark:text-white">{{ $u->code }}</span>
                        <span class="shrink-0 whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $u->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-white/10 dark:text-slate-300' }}">
                            {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs leading-snug text-slate-700 dark:text-slate-300">{{ $u->name }}</p>
                    <p class="mt-2 font-mono text-[11px] text-slate-600 dark:text-slate-400">{{ $u->staff_count }} pegawai aktif</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Tahun ajaran & semester aktif --}}
    <section class="portal-card p-5">
        <h3 class="label-caps">Tahun Ajaran &amp; Semester Aktif</h3>
        <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">
            Periode aktif menentukan konteks seluruh data akademik dan operasional kampus.
        </p>

        <form action="{{ route('admin.master.period') }}" method="POST" class="mt-4 grid gap-3 text-xs sm:grid-cols-3">
            @csrf
            <div>
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Tahun Ajaran</label>
                <select name="academic_year_id" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                    @foreach($academicYears as $y)
                        <option value="{{ $y->id }}" @selected($y->is_active)>{{ $y->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block font-bold text-slate-700 dark:text-slate-300">Semester</label>
                <select name="semester_id" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-slate-900 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                    @foreach($semesters as $s)
                        <option value="{{ $s->id }}" @selected($s->is_active)>{{ ucfirst($s->name) }} &mdash; {{ $s->year_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-2 font-bold text-white transition hover:bg-blue-600">
                    <i class="bi bi-calendar-check-fill"></i>
                    <span>Tetapkan Periode</span>
                </button>
            </div>
        </form>

        <div class="mt-4 grid gap-2 border-t border-slate-100 pt-4 text-xs dark:border-white/10 sm:grid-cols-2">
            @foreach($semesters as $s)
                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-white/5">
                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ ucfirst($s->name) }} &mdash; {{ $s->year_name }}</span>
                    <span class="shrink-0 font-mono text-[11px] text-slate-600 dark:text-slate-400">
                        {{ \Carbon\Carbon::parse($s->start_date)->format('d/m/y') }} &ndash; {{ \Carbon\Carbon::parse($s->end_date)->format('d/m/y') }}
                    </span>
                    @if($s->is_active)
                        <span class="shrink-0 whitespace-nowrap rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300">Aktif</span>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    {{-- Jenis perizinan & pengaturan sistem --}}
    <section class="grid gap-4 lg:grid-cols-2">
        <article class="portal-card p-5">
            <h3 class="label-caps">Jenis Perizinan Terdaftar</h3>
            <ul class="mt-4 space-y-2 text-sm">
                @foreach($leaveTypes as $lt)
                    <li class="flex items-center gap-2 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-white/5">
                        <i class="bi bi-file-earmark-check text-gold-600 dark:text-gold-400"></i>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $lt->name }}</span>
                    </li>
                @endforeach
            </ul>
        </article>

        <article class="portal-card p-5">
            <h3 class="label-caps">Parameter Sistem</h3>
            <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">Batas karakter, ukuran lampiran, dan aturan tampilan.</p>

            <div class="mt-4 space-y-2">
                @foreach($settings as $st)
                    <form action="{{ route('admin.master.setting') }}" method="POST"
                        class="flex items-center gap-2 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-white/5">
                        @csrf
                        <input type="hidden" name="setting_key" value="{{ $st->setting_key }}">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-mono text-[11px] font-semibold text-slate-800 dark:text-slate-200">{{ $st->setting_key }}</p>
                            <p class="truncate text-[10px] text-slate-600 dark:text-slate-400">{{ $st->description ?? $st->setting_group }}</p>
                        </div>
                        <input type="text" name="setting_value" value="{{ $st->setting_value }}" required maxlength="500"
                            class="w-24 shrink-0 rounded-lg border border-slate-300 bg-white px-2 py-1 font-mono text-[11px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-white/15 dark:bg-slate-950 dark:text-white">
                        <button type="submit" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg bg-ink-900 text-white transition hover:bg-ink-800" title="Simpan">
                            <i class="bi bi-check-lg text-xs"></i>
                        </button>
                    </form>
                @endforeach
            </div>
        </article>
    </section>
</div>
@endsection
