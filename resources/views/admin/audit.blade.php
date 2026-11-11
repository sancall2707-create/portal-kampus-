@extends('layouts.app')

@section('title', 'Jejak Audit — Panel Koordinator')

@section('content')
<div class="w-full space-y-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

    <div>
        <p class="label-caps">Pusat Kendali</p>
        <h2 class="mt-1 font-display text-2xl text-slate-900 dark:text-white sm:text-3xl">Jejak Audit Sistem</h2>
        <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
            Riwayat tindakan administratif, akses login, dan simulasi akun untuk keperluan audit dan evaluasi keamanan.
        </p>
    </div>

    @include('admin.partials.nav', ['active' => 'audit'])

    {{-- Tab --}}
    @php
        $auditTabs = [
            'activity'      => ['Aktivitas Administratif', 'bi-clock-history', $activities->count()],
            'logins'        => ['Riwayat Login', 'bi-box-arrow-in-right', $logins->count()],
            'impersonation' => ['Simulasi Akun', 'bi-person-badge', $impersonations->count()],
        ];
    @endphp
    <div class="no-scrollbar -mx-1 flex items-center gap-2 overflow-x-auto px-1 pb-1 text-xs">
        @foreach($auditTabs as $key => [$label, $icon, $count])
            <a href="{{ route('admin.audit', ['tab' => $key]) }}"
               class="flex shrink-0 items-center gap-2 whitespace-nowrap rounded-xl border px-3.5 py-2 font-bold transition {{ $tab === $key ? 'border-blue-600 bg-blue-700 text-white shadow' : 'border-slate-200 bg-white text-slate-700 hover:text-slate-900 dark:border-white/15 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white' }}">
                <i class="bi {{ $icon }}"></i>
                <span>{{ $label }}</span>
                <span class="rounded-full px-1.5 py-0.5 font-mono text-[10px] {{ $tab === $key ? 'bg-white/20' : 'bg-slate-100 dark:bg-white/10' }}">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    {{-- Aktivitas administratif --}}
    @if($tab === 'activity')
        <section class="portal-card overflow-hidden">
            <header class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                <h3 class="font-display text-base text-slate-900 dark:text-white">Aktivitas Administratif</h3>
                <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">100 tindakan terakhir yang tercatat sistem.</p>
            </header>
            <ul class="feed-divide">
                @forelse($activities as $a)
                    <li class="flex items-start gap-3 px-5 py-3.5">
                        <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-slate-100 text-blue-800 dark:bg-white/10 dark:text-blue-300">
                            <i class="bi bi-clock-history text-sm"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $a->description }}</p>
                            <p class="mt-0.5 font-mono text-[11px] text-slate-600 dark:text-slate-400">
                                {{ $a->causer_name ?? 'Sistem' }}
                                @if($a->causer_nip) ({{ $a->causer_nip }}) @endif
                                &middot; {{ $a->event ?? $a->log_name }}
                                &middot; {{ \Carbon\Carbon::parse($a->created_at)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-12 text-center">
                        <i class="bi bi-clock-history text-2xl text-slate-400"></i>
                        <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Belum ada aktivitas administratif tercatat.</p>
                    </li>
                @endforelse
            </ul>
        </section>
    @endif

    {{-- Riwayat login --}}
    @if($tab === 'logins')
        <section class="portal-card overflow-hidden">
            <header class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                <h3 class="font-display text-base text-slate-900 dark:text-white">Riwayat Akses Login</h3>
                <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">Termasuk upaya login yang gagal untuk deteksi penyalahgunaan.</p>
            </header>

            <div class="divide-y divide-slate-100 dark:divide-white/10 sm:hidden">
                @forelse($logins as $l)
                    <div class="flex items-start justify-between gap-3 p-4">
                        <div class="min-w-0">
                            <p class="break-words text-sm font-semibold text-slate-900 dark:text-white">{{ $l->name ?? $l->identifier }}</p>
                            <p class="mt-0.5 font-mono text-[11px] text-slate-600 dark:text-slate-400">{{ $l->ip_address }} &middot; {{ $l->method }}</p>
                            <p class="mt-0.5 font-mono text-[10px] text-slate-600 dark:text-slate-400">{{ \Carbon\Carbon::parse($l->logged_in_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="shrink-0 whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $l->success ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-500/15 dark:text-rose-300' }}">
                            {{ $l->success ? 'Berhasil' : 'Gagal' }}
                        </span>
                    </div>
                @empty
                    <p class="p-8 text-center text-xs text-slate-600 dark:text-slate-400">Belum ada riwayat login.</p>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full border-collapse text-left text-xs">
                    <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-300">
                        <tr>
                            <th class="px-5 py-3">Pengguna</th>
                            <th class="px-5 py-3">Identitas Masuk</th>
                            <th class="px-5 py-3">Metode</th>
                            <th class="px-5 py-3">Alamat IP</th>
                            <th class="px-5 py-3">Waktu</th>
                            <th class="px-5 py-3">Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 dark:divide-white/10 dark:text-slate-300">
                        @forelse($logins as $l)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $l->name ?? '—' }}</td>
                                <td class="px-5 py-3 font-mono">{{ $l->identifier }}</td>
                                <td class="px-5 py-3">
                                    <span class="whitespace-nowrap rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-700 dark:bg-white/10 dark:text-slate-200">{{ $l->method }}</span>
                                </td>
                                <td class="px-5 py-3 font-mono text-[11px]">{{ $l->ip_address }}</td>
                                <td class="px-5 py-3 font-mono text-[11px]">{{ \Carbon\Carbon::parse($l->logged_in_at)->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3">
                                    <span class="whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $l->success ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-500/15 dark:text-rose-300' }}">
                                        {{ $l->success ? 'Berhasil' : 'Gagal' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-12 text-center text-xs text-slate-700 dark:text-slate-300">Belum ada riwayat login.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    {{-- Simulasi akun --}}
    @if($tab === 'impersonation')
        <section class="portal-card overflow-hidden">
            <header class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                <h3 class="font-display text-base text-slate-900 dark:text-white">Riwayat Simulasi Akun</h3>
                <p class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">Setiap sesi masuk sebagai pengguna lain wajib menyertakan alasan.</p>
            </header>
            <ul class="feed-divide">
                @forelse($impersonations as $imp)
                    <li class="px-5 py-3.5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-slate-900 dark:text-white">
                                    <span class="font-semibold">{{ $imp->impersonator_name }}</span>
                                    <span class="text-slate-600 dark:text-slate-400"> masuk sebagai </span>
                                    <span class="font-semibold text-blue-800 dark:text-blue-300">{{ $imp->impersonated_name }}</span>
                                </p>
                                <p class="mt-1 text-xs leading-relaxed text-slate-700 dark:text-slate-300">{{ $imp->reason }}</p>
                                <p class="mt-1 font-mono text-[11px] text-slate-600 dark:text-slate-400">
                                    {{ $imp->impersonated_nip }} &middot; {{ $imp->ip_address }}
                                    &middot; {{ \Carbon\Carbon::parse($imp->started_at)->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <span class="shrink-0 whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $imp->ended_at ? 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300' }}">
                                {{ $imp->ended_at ? 'Selesai' : 'Berlangsung' }}
                            </span>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-12 text-center">
                        <i class="bi bi-person-badge text-2xl text-slate-400"></i>
                        <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-300">Belum ada sesi simulasi akun.</p>
                    </li>
                @endforelse
            </ul>
        </section>
    @endif
</div>
@endsection
