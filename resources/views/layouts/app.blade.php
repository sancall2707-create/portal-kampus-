<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIAP PL Deltamas')</title>

    @include('partials.design-head')

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#102433">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIAP PL">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
</head>
<body class="bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 min-h-screen antialiased selection:bg-blue-800 selection:text-white">
@auth
    @php
        $portalUser = Auth::user();
        $portalRole = ucfirst(str_replace('_', ' ', $portalUser->getRoleNames()->first() ?? 'Staf'));
        $portalUnits = $portalUser->units->isNotEmpty()
            ? 'Unit '.$portalUser->units->pluck('code')->implode(' & ')
            : 'Kampus Deltamas';
        $navGroups = [
            'Pusat Informasi' => [
                ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'bi-grid-1x2', 'label' => 'Dasbor'],
                ['route' => 'announcements.index', 'pattern' => 'announcements.*', 'icon' => 'bi-megaphone', 'label' => 'Pengumuman'],
                ['route' => 'agendas.index', 'pattern' => 'agendas.*', 'icon' => 'bi-calendar3', 'label' => 'Agenda Kampus'],
            ],
            'Layanan Pegawai' => [
                ['route' => 'leaves.index', 'pattern' => 'leaves.*', 'icon' => 'bi-file-earmark-check', 'label' => 'Perizinan & Guru Inval'],
                ['route' => 'facilities.index', 'pattern' => 'facilities.*', 'icon' => 'bi-building', 'label' => 'Reservasi Fasilitas'],
                ['route' => 'letters.index', 'pattern' => 'letters.*', 'icon' => 'bi-envelope-paper', 'label' => 'Tata Persuratan & Arsip'],
            ],
            'Operasional' => [
                ['route' => 'operational.index', 'pattern' => 'operational.*', 'icon' => 'bi-tools', 'label' => 'Operasional Lapangan'],
            ],
            'Komunikasi' => [
                ['route' => 'messages.index', 'pattern' => 'messages.*', 'icon' => 'bi-chat-left-text', 'label' => 'Pesan'],
                ['route' => 'greeting.show', 'pattern' => 'greeting.show', 'icon' => 'bi-chat-quote', 'label' => 'Sapaan Koordinator'],
            ],
        ];
    @endphp

    <div id="navDrawerBackdrop" onclick="toggleNavDrawer()" class="fixed inset-0 bg-ink-950/70 backdrop-blur-sm z-40 transition-opacity duration-300 opacity-0 pointer-events-none lg:hidden"></div>

    <aside id="navDrawer" class="fixed inset-y-0 left-0 z-50 flex w-[17.5rem] max-w-[86vw] -translate-x-full flex-col border-r border-white/10 bg-ink-950 text-slate-200 transition-transform duration-300 ease-out lg:translate-x-0">
        <div class="flex items-center justify-between gap-3 border-b border-white/10 px-4 py-4">
            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3" onclick="closeNavDrawer()">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-white p-1 shadow-sm ring-2 ring-gold-500/80">
                    <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo PL Deltamas" class="h-full w-full rounded-full object-contain" />
                </span>
                <span class="min-w-0 text-left">
                    <span class="block truncate font-display text-base font-semibold leading-tight text-white">SIAP PL Deltamas</span>
                    <span class="mt-0.5 block truncate text-[11px] text-slate-400">{{ $portalUnits }}</span>
                </span>
            </a>
            <button type="button" onclick="toggleNavDrawer()" class="grid h-9 w-9 place-items-center rounded-lg text-slate-300 hover:bg-white/10 lg:hidden" title="Tutup menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="mx-4 mt-4 rounded-2xl border border-white/10 bg-white/5 px-3 py-3">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gold-500/20 font-semibold text-gold-300">
                    {{ mb_substr($portalUser->name, 0, 2) }}
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-semibold text-white">{{ $portalUser->name }}</span>
                    <span class="block truncate font-mono text-[11px] text-slate-400">{{ $portalUser->nip ?? $portalUser->email }}</span>
                    <span class="mt-1 inline-flex rounded-full bg-gold-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gold-300">{{ $portalRole }}</span>
                </span>
            </div>
        </div>

        <nav class="portal-scroll mt-3 flex-1 space-y-4 overflow-y-auto px-3 pb-4">
            @foreach($navGroups as $group => $items)
                <div>
                    <p class="label-caps px-3 pb-2">{{ $group }}</p>
                    <div class="space-y-0.5">
                        @foreach($items as $item)
                            @php $active = request()->routeIs($item['pattern']); @endphp
                            <a href="{{ route($item['route']) }}" onclick="closeNavDrawer()"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition {{ $active ? 'bg-white text-ink-950 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                <i class="bi {{ $item['icon'] }} w-5 text-center text-base {{ $active ? 'text-blue-700' : 'text-gold-400' }}"></i>
                                <span class="leading-snug">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div>
                <p class="label-caps px-3 pb-2">Lainnya</p>
                <button type="button" onclick="showDevelopmentNotice()" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    <i class="bi bi-book-half w-5 text-center text-base text-gold-400"></i>
                    <span>Kurikulum</span>
                </button>
                <button type="button" onclick="openNotificationModal()" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-left text-[13px] font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    <span class="flex items-center gap-3">
                        <i class="bi bi-bell w-5 text-center text-base text-gold-400"></i>
                        <span>Notifikasi Smartphone</span>
                    </span>
                    <span id="drawerPushTag" class="rounded-full bg-white/10 px-2 py-0.5 font-mono text-[10px] text-slate-300">Cek</span>
                </button>
            </div>
        </nav>

        <div class="border-t border-white/10 p-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-rose-300/30 bg-rose-500/10 px-4 py-2.5 text-xs font-semibold text-rose-200 transition hover:bg-rose-500/20">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar dari Akun</span>
                </button>
            </form>
        </div>
    </aside>
@endauth

    <div class="flex min-h-screen flex-col pb-[5.5rem] lg:pb-0 lg:pl-[17.5rem]">
        <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-slate-50/90 backdrop-blur dark:border-slate-800 dark:bg-slate-950/85">
            <div class="flex h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                <div class="min-w-0">
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 lg:hidden">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white p-0.5 ring-1 ring-slate-200 dark:ring-white/20">
                                <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo PL Deltamas" class="h-full w-full rounded-full object-contain" />
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-slate-900 dark:text-white">SIAP PL Deltamas</span>
                                <span class="block truncate text-[11px] text-slate-600 dark:text-slate-300">{{ $portalUnits }}</span>
                            </span>
                        </a>
                        <div class="hidden lg:block">
                            <p class="label-caps">Kampus Pangudi Luhur Deltamas</p>
                            <p class="truncate text-sm text-slate-600 dark:text-slate-300">{{ $portalRole }} · {{ $portalUnits }}</p>
                        </div>
                    @else
                        <a href="{{ route('landing') }}" class="font-display text-lg font-semibold text-slate-900 dark:text-white">SIAP PL Deltamas</a>
                    @endauth
                </div>

                <div class="flex items-center gap-2">
                    @auth
                    <button type="button" onclick="openNotificationModal()" class="relative grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:border-gold-400 dark:border-white/15 dark:bg-white/5 dark:text-slate-200 dark:hover:border-gold-400" title="Pengaturan Notifikasi Smartphone">
                        <i class="bi bi-bell text-base"></i>
                        <span id="pushHeaderDot" class="absolute right-2 top-2 hidden h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900"></span>
                    </button>
                    @endauth

                    <button type="button" onclick="toggleTheme()" class="grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:border-gold-400 dark:border-white/15 dark:bg-white/5 dark:text-slate-200 dark:hover:border-gold-400" title="Ganti Tema">
                        <i id="themeIconSun" class="bi bi-sun-fill hidden text-gold-400 dark:inline"></i>
                        <i id="themeIconMoon" class="bi bi-moon-stars-fill inline dark:hidden"></i>
                    </button>

                    @auth
                    <button type="button" onclick="toggleNavDrawer()" class="grid h-10 w-10 place-items-center rounded-full bg-ink-900 text-white lg:hidden" title="Buka Menu Navigasi">
                        <i class="bi bi-list text-lg leading-none"></i>
                    </button>
                    @endauth
                </div>
            </div>
        </header>

        <main class="w-full min-w-0 flex-1">
            @yield('content')
        </main>

        <footer class="border-t border-slate-200 px-4 py-4 text-center text-xs text-slate-600 dark:border-slate-800 dark:text-slate-300">
            &copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · SIAP PL Deltamas
        </footer>
    </div>

    @auth
    <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-950/95 lg:hidden">
        <div class="grid grid-cols-4">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 px-2 py-2.5 text-[11px] font-semibold {{ request()->routeIs('dashboard') ? 'text-blue-700 dark:text-blue-300' : 'text-slate-600 dark:text-slate-300' }}">
                <i class="bi bi-house-door text-base"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('announcements.index') }}" class="flex flex-col items-center gap-1 px-2 py-2.5 text-[11px] font-semibold {{ request()->routeIs('announcements.*') ? 'text-blue-700 dark:text-blue-300' : 'text-slate-600 dark:text-slate-300' }}">
                <i class="bi bi-megaphone text-base"></i>
                <span>Siaran</span>
            </a>
            <a href="{{ route('agendas.index') }}" class="flex flex-col items-center gap-1 px-2 py-2.5 text-[11px] font-semibold {{ request()->routeIs('agendas.*') ? 'text-blue-700 dark:text-blue-300' : 'text-slate-600 dark:text-slate-300' }}">
                <i class="bi bi-calendar3 text-base"></i>
                <span>Agenda</span>
            </a>
            <button type="button" onclick="toggleNavDrawer()" class="flex flex-col items-center gap-1 px-2 py-2.5 text-[11px] font-semibold text-slate-600 dark:text-slate-300">
                <i class="bi bi-grid text-base"></i>
                <span>Menu</span>
            </button>
        </div>
    </nav>
    @endauth

    <div id="pushNotificationModal" class="fixed inset-0 z-50 hidden items-end justify-center bg-ink-950/70 p-4 backdrop-blur-sm sm:items-center">
        <div class="w-full max-w-md space-y-4 rounded-3xl border border-slate-200 bg-white p-6 text-left shadow-2xl dark:border-slate-800 dark:bg-slate-900 sm:p-7">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="grid h-10 w-10 place-items-center rounded-2xl bg-blue-100 text-xl text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                        <i class="bi bi-bell"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Notifikasi Smartphone</h3>
                        <p class="text-[11px] font-medium text-slate-600 dark:text-slate-300">PWA Web Push Notification</p>
                    </div>
                </div>
                <button type="button" onclick="closeNotificationModal()" class="grid h-8 w-8 place-items-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <div class="space-y-2 rounded-2xl border border-slate-200 bg-slate-50 p-3.5 dark:border-slate-700 dark:bg-slate-800/80">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-medium text-slate-600 dark:text-slate-300">Status di HP ini:</span>
                    <span id="pushStatusBadge" class="rounded-full border border-amber-200 bg-amber-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/20 dark:text-amber-300">
                        Memeriksa...
                    </span>
                </div>
                <p id="pushStatusDescription" class="text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                    Notifikasi web push memungkinkan pesan siaran kampus, izin, dan peringatan darurat masuk langsung ke layar smartphone Anda.
                </p>
            </div>

            <div class="space-y-2 pt-1">
                <button type="button" id="btnSubscribePush" onclick="handleSubscribePush()" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 py-2.5 px-4 text-xs font-semibold text-white transition hover:bg-blue-600">
                    <i class="bi bi-bell"></i>
                    <span>Aktifkan Notifikasi di HP Ini</span>
                </button>
                <button type="button" id="btnTestPush" onclick="handleSendTestPush()" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 py-2.5 px-4 text-xs font-semibold text-white transition hover:bg-emerald-600">
                    <i class="bi bi-send-check"></i>
                    <span>Kirim Notifikasi Uji Coba Sekarang</span>
                </button>
            </div>

            <div id="pushFeedbackBox" class="hidden items-start gap-2 rounded-xl p-3 text-xs"></div>

            <div class="space-y-1 border-t border-slate-100 pt-3 text-[11px] text-slate-600 dark:text-slate-300 dark:border-slate-800">
                <div class="font-semibold text-slate-700 dark:text-slate-300">Petunjuk Khusus Smartphone:</div>
                <ul class="list-disc space-y-0.5 pl-4">
                    <li><strong>Android (Chrome):</strong> Tekan "Aktifkan" lalu pilih <em>"Izinkan" (Allow)</em> saat pop-up browser muncul.</li>
                    <li><strong>iPhone / iOS:</strong> Wajib pasang aplikasi lewat menu <em>Share → Add to Home Screen</em> terlebih dahulu (syarat resmi iOS 16.4+).</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="devNoticeModal" class="fixed inset-0 z-50 hidden items-end justify-center bg-ink-950/70 p-4 backdrop-blur-sm sm:items-center">
        <div class="w-full max-w-sm space-y-4 rounded-3xl border border-slate-200 bg-white p-6 text-center shadow-2xl dark:border-slate-800 dark:bg-slate-900 sm:p-7">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-full border border-blue-200 bg-blue-50 text-3xl text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/20 dark:text-blue-300">
                <i class="bi bi-info-circle"></i>
            </div>
            <div class="space-y-1.5">
                <h3 class="font-display text-2xl text-slate-900 dark:text-white">Modul Kurikulum</h3>
                <p class="pt-1 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                    Fitur ini <strong class="text-slate-900 dark:text-white">masih dalam pengembangan</strong>.
                </p>
            </div>
            <button type="button" onclick="closeDevelopmentNotice()" class="w-full rounded-xl bg-ink-900 py-2.5 px-4 text-xs font-semibold text-white transition hover:bg-ink-800">
                Mengerti & Tutup
            </button>
        </div>
    </div>

    <script>
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }

        function isDesktopNav() {
            return window.matchMedia('(min-width: 1024px)').matches;
        }

        function toggleNavDrawer() {
            const drawer = document.getElementById('navDrawer');
            const backdrop = document.getElementById('navDrawerBackdrop');
            if (!drawer || !backdrop || isDesktopNav()) return;

            const isClosed = drawer.classList.contains('-translate-x-full');
            if (isClosed) {
                drawer.classList.remove('-translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100');
            } else {
                closeNavDrawer();
            }
        }

        function closeNavDrawer() {
            const drawer = document.getElementById('navDrawer');
            const backdrop = document.getElementById('navDrawerBackdrop');
            if (!drawer || !backdrop || isDesktopNav()) return;
            drawer.classList.add('-translate-x-full');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            backdrop.classList.remove('opacity-100');
        }

        function showDevelopmentNotice() {
            const modal = document.getElementById('devNoticeModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
            closeNavDrawer();
        }

        function closeDevelopmentNotice() {
            const modal = document.getElementById('devNoticeModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function openNotificationModal() {
            const modal = document.getElementById('pushNotificationModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
            checkNotificationStatus();
            closeNavDrawer();
        }

        function closeNotificationModal() {
            const modal = document.getElementById('pushNotificationModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        async function checkNotificationStatus() {
            const badge = document.getElementById('pushStatusBadge');
            const desc = document.getElementById('pushStatusDescription');
            const dot = document.getElementById('pushHeaderDot');
            const drawerTag = document.getElementById('drawerPushTag');
            const btnSubscribe = document.getElementById('btnSubscribePush');

            if (!('Notification' in window) || !('serviceWorker' in navigator)) {
                if (badge) {
                    badge.textContent = 'Tidak Didukung';
                    badge.className = 'rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-slate-600';
                }
                if (desc) desc.textContent = 'Browser ini tidak mendukung Web Push Notification.';
                if (btnSubscribe) btnSubscribe.disabled = true;
                return;
            }

            if (Notification.permission === 'granted') {
                if (badge) {
                    badge.textContent = 'Aktif & Terhubung';
                    badge.className = 'rounded-full border border-emerald-200 bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/20 dark:text-emerald-300';
                }
                if (desc) desc.textContent = 'Smartphone Anda sudah siap menerima notifikasi instan dari kampus Pangudi Luhur.';
                if (dot) dot.classList.remove('hidden');
                if (drawerTag) {
                    drawerTag.textContent = 'Aktif';
                    drawerTag.className = 'rounded-full bg-emerald-500/20 px-2 py-0.5 font-mono text-[10px] font-bold text-emerald-300';
                }
                if (btnSubscribe) {
                    btnSubscribe.innerHTML = '<i class="bi bi-arrow-repeat"></i><span>Sinkronkan Ulang Perangkat Ini</span>';
                    btnSubscribe.className = 'flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-slate-100 py-2.5 px-4 text-xs font-semibold text-slate-800 transition hover:bg-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200';
                }
            } else if (Notification.permission === 'denied') {
                if (badge) {
                    badge.textContent = 'Diblokir';
                    badge.className = 'rounded-full border border-rose-200 bg-rose-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/20 dark:text-rose-300';
                }
                if (desc) desc.textContent = 'Izin notifikasi diblokir pada browser HP ini. Buka ikon gembok/setelan situs di browser untuk mengizinkan notifikasi.';
                if (dot) dot.classList.add('hidden');
            } else {
                if (badge) {
                    badge.textContent = 'Belum Aktif';
                    badge.className = 'rounded-full border border-amber-200 bg-amber-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/20 dark:text-amber-300';
                }
                if (desc) desc.textContent = 'Ketuk tombol di bawah untuk mengaktifkan notifikasi di smartphone ini.';
                if (dot) dot.classList.add('hidden');
            }
        }

        async function handleSubscribePush() {
            const feedback = document.getElementById('pushFeedbackBox');
            const btn = document.getElementById('btnSubscribePush');
            if (feedback) feedback.classList.add('hidden');

            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                alert('Browser ini tidak mendukung Push Notification.');
                return;
            }

            try {
                if (btn) btn.disabled = true;
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    if (feedback) {
                        feedback.className = 'flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300';
                        feedback.innerHTML = '<i class="bi bi-x-circle-fill"></i><span>Izin notifikasi ditolak oleh pengguna.</span>';
                        feedback.classList.remove('hidden');
                    }
                    checkNotificationStatus();
                    if (btn) btn.disabled = false;
                    return;
                }

                const registration = await navigator.serviceWorker.ready;
                const keyRes = await fetch('{{ route("push.key") }}');
                const keyData = await keyRes.json();
                const convertedKey = urlBase64ToUint8Array(keyData.publicKey);

                let subscription = await registration.pushManager.getSubscription();
                if (subscription) {
                    await subscription.unsubscribe();
                }

                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: convertedKey
                });

                const subJson = subscription.toJSON();
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                await fetch('{{ route("push.subscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(subJson)
                });

                if (feedback) {
                    feedback.className = 'flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300';
                    feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>Smartphone Anda berhasil didaftarkan untuk menerima notifikasi!</span>';
                    feedback.classList.remove('hidden');
                }
                checkNotificationStatus();
            } catch (err) {
                console.error('Push registration error:', err);
                if (feedback) {
                    feedback.className = 'flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300';
                    feedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i><span>Gagal mendaftarkan notifikasi: ' + (err.message || 'Kesalahan jaringan') + '</span>';
                    feedback.classList.remove('hidden');
                }
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        async function handleSendTestPush() {
            const feedback = document.getElementById('pushFeedbackBox');
            const btn = document.getElementById('btnTestPush');
            if (feedback) feedback.classList.add('hidden');

            try {
                if (btn) btn.disabled = true;
                if (Notification.permission !== 'granted') {
                    await handleSubscribePush();
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('{{ route("push.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        title: 'SIAP PL Deltamas (Uji Coba)',
                        body: 'Pemberitahuan berhasil masuk ke smartphone Anda pada ' + new Date().toLocaleTimeString('id-ID') + ' WIB.',
                        url: '{{ route("dashboard") }}'
                    })
                });

                const data = await res.json();

                if (data.success && data.sent > 0) {
                    if (feedback) {
                        feedback.className = 'flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300';
                        feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>Notifikasi uji coba terkirim! Cek status bar atau layar kunci smartphone Anda.</span>';
                        feedback.classList.remove('hidden');
                    }
                } else {
                    if (Notification.permission === 'granted') {
                        const reg = await navigator.serviceWorker.ready;
                        reg.showNotification('SIAP PL Deltamas (Lokal)', {
                            body: 'Notifikasi instan aktif di browser smartphone Anda.',
                            icon: '/icons/icon-192x192.png',
                            badge: '/icons/icon-192x192.png',
                            vibrate: [200, 100, 200]
                        });
                    }
                    if (feedback) {
                        feedback.className = 'flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300';
                        feedback.innerHTML = '<i class="bi bi-info-circle-fill"></i><span>' + (data.message || 'Notifikasi lokal dipicu langsung di browser.') + '</span>';
                        feedback.classList.remove('hidden');
                    }
                }
            } catch (err) {
                console.error('Send test push error:', err);
                if (feedback) {
                    feedback.className = 'flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300';
                    feedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i><span>Gagal mengirim notifikasi: ' + err.message + '</span>';
                    feedback.classList.remove('hidden');
                }
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(() => {
                    checkNotificationStatus();
                }).catch(() => {});
            });
        }
    </script>
</body>
</html>
