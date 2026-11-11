@php
    $adminTabs = [
        'dashboard'   => ['route' => 'admin.dashboard',   'icon' => 'bi-speedometer2',   'label' => 'Ringkasan'],
        'users'       => ['route' => 'admin.users',       'icon' => 'bi-people-fill',    'label' => 'Akun Pegawai'],
        'delegations' => ['route' => 'admin.delegations', 'icon' => 'bi-person-fill-up', 'label' => 'Delegasi Wewenang'],
        'audit'       => ['route' => 'admin.audit',       'icon' => 'bi-shield-check',   'label' => 'Jejak Audit'],
        'master'      => ['route' => 'admin.master',      'icon' => 'bi-diagram-3-fill', 'label' => 'Data Induk'],
    ];
@endphp

<nav class="no-scrollbar -mx-1 flex items-center gap-2 overflow-x-auto px-1 pb-1 text-xs">
    @foreach($adminTabs as $key => $tab)
        <a href="{{ route($tab['route']) }}"
           class="flex shrink-0 items-center gap-2 whitespace-nowrap rounded-xl px-3.5 py-2 font-bold transition {{ $active === $key ? 'bg-ink-900 text-white shadow' : 'bg-white text-slate-700 hover:text-slate-900 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white' }}">
            <i class="bi {{ $tab['icon'] }}"></i>
            <span>{{ $tab['label'] }}</span>
        </a>
    @endforeach
</nav>
