<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIAP PL Deltamas')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                            950: '#172554',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIAP PL">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

    <script>
        // Init theme immediately to prevent flash
        if (localStorage.theme === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col justify-between antialiased transition-colors duration-200 selection:bg-blue-600 selection:text-white">

    <!-- Top App Bar -->
    <header class="border-b border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur sticky top-0 z-40 transition-colors shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Left: Logo & Portal Title -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                    <div class="h-10 w-10 sm:h-11 sm:w-11 rounded-full bg-white/90 dark:bg-white/10 p-0.5 shadow-sm border border-slate-200 dark:border-white/15 flex items-center justify-center shrink-0 overflow-hidden">
                        <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo" class="h-full w-full object-contain rounded-full drop-shadow-xs">
                    </div>
                    <div>
                        <h1 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                            SIAP PL Deltamas
                        </h1>
                        <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate max-w-[170px] sm:max-w-xs">
                            @if(isset($user) && $user->units->isNotEmpty())
                                Unit {{ $user->units->pluck('code')->implode(' & ') }}
                            @else
                                Kampus Deltamas
                            @endif
                        </p>
                    </div>
                </a>
            </div>

            <!-- Right: Actions (Push Notification, Theme, Menu) -->
            <div class="flex items-center space-x-2">
                
                @auth
                <!-- Notification Bell Button -->
                <button type="button" onclick="openNotificationModal()" class="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition" title="Pengaturan Notifikasi Smartphone">
                    <i class="bi bi-bell-fill text-blue-600 dark:text-blue-400 text-base"></i>
                    <span id="pushHeaderDot" class="hidden absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900"></span>
                </button>
                @endauth

                <!-- Theme Toggle Button -->
                <button type="button" onclick="toggleTheme()" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition" title="Ganti Tema">
                    <i id="themeIconSun" class="bi bi-sun-fill text-amber-500 hidden dark:inline"></i>
                    <i id="themeIconMoon" class="bi bi-moon-stars-fill text-slate-600 dark:hidden inline"></i>
                </button>

                @auth
                <!-- Hamburger Menu Button -->
                <button type="button" onclick="toggleNavDrawer()" class="p-2 sm:px-3 sm:py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-md shadow-blue-600/20" title="Buka Menu Navigasi">
                    <i class="bi bi-list text-lg leading-none"></i>
                    <span class="hidden sm:inline">Menu</span>
                </button>
                @endauth
            </div>
        </div>
    </header>

    @auth
    <!-- Slide-over Off-canvas Drawer Navigation (Hamburger Menu) -->
    <div id="navDrawerBackdrop" onclick="toggleNavDrawer()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none"></div>

    <aside id="navDrawer" class="fixed top-0 right-0 bottom-0 w-80 max-w-[85vw] bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 z-50 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col justify-between">
        
        <!-- Drawer Content -->
        <div>
            <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center font-bold text-white shadow-md">
                        <i class="bi bi-grid-fill"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">Menu Navigasi</h2>
                        <span class="text-[10px] uppercase font-semibold text-blue-600 dark:text-blue-400">SIAP PL Deltamas</span>
                    </div>
                </div>
                <button type="button" onclick="toggleNavDrawer()" class="p-2 rounded-lg text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="bi bi-x-lg text-base"></i>
                </button>
            </div>

            <!-- Profile Info in Drawer -->
            <div class="p-4 mx-4 mt-4 rounded-2xl bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center text-sm shadow">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono truncate">{{ Auth::user()->nip ?? Auth::user()->email }}</div>
                        <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[9px] uppercase font-bold bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300">
                            {{ ucfirst(str_replace('_', ' ', Auth::user()->getRoleNames()->first() ?? 'Staf')) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Nav Links List -->
            <nav class="p-4 space-y-1.5 overflow-y-auto max-h-[calc(100vh-280px)]">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-house-door-fill text-base w-5 text-center"></i>
                    <span>Dashboard Beranda</span>
                </a>

                <a href="{{ route('announcements.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('announcements.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-megaphone-fill text-base w-5 text-center text-blue-500"></i>
                    <span>Papan Siaran (Broadcast)</span>
                </a>

                <a href="{{ route('agendas.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('agendas.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-calendar3 text-base w-5 text-center text-emerald-500"></i>
                    <span>Kalender Agenda Kampus</span>
                </a>

                <a href="{{ route('leaves.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('leaves.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-file-earmark-check-fill text-base w-5 text-center text-amber-500"></i>
                    <span>Perizinan & Guru Inval</span>
                </a>

                <a href="{{ route('facilities.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('facilities.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-building text-base w-5 text-center text-indigo-500"></i>
                    <span>Fasilitas & E-Ticket QR</span>
                </a>

                <a href="{{ route('operational.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('operational.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-tools text-base w-5 text-center text-cyan-500"></i>
                    <span>Operasional Lapangan</span>
                </a>

                <a href="{{ route('letters.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('letters.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-envelope-paper-fill text-base w-5 text-center text-slate-400"></i>
                    <span>Tata Persuratan & Arsip</span>
                </a>

                <a href="{{ route('messages.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('messages.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-chat-left-lock-fill text-base w-5 text-center text-purple-500"></i>
                    <span>Pesan</span>
                </a>

                <!-- Kurikulum Drawer Button -->
                <button type="button" onclick="showDevelopmentNotice()" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition text-left group">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-book-half text-base w-5 text-center text-blue-600 dark:text-blue-400"></i>
                        <span>Kurikulum</span>
                    </div>
                </button>

                <!-- Push Notification Trigger in Drawer -->
                <button type="button" onclick="openNotificationModal()" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition text-left group">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-bell-fill text-base w-5 text-center text-amber-500"></i>
                        <span>Notifikasi Smartphone</span>
                    </div>
                    <span id="drawerPushTag" class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">Cek</span>
                </button>

                <div class="pt-2 border-t border-slate-200 dark:border-slate-800 mt-2">
                    <a href="{{ route('greeting.show') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="bi bi-chat-quote-fill text-amber-500 w-5 text-center"></i>
                        <span>Sapaan Koordinator</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Drawer Footer: Logout Button -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30 text-xs font-bold transition flex items-center justify-center gap-2">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar dari Akun</span>
                </button>
            </form>
        </div>
    </aside>
    @endauth

    <!-- Main Content Yield -->
    <main class="flex-1 w-full">
        @yield('content')
    </main>

    <!-- Modal Notifikasi Smartphone (Web Push PWA) -->
    <div id="pushNotificationModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden transition-opacity">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 w-full max-w-md space-y-4 shadow-2xl text-left">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="h-10 w-10 rounded-2xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shadow-inner">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Notifikasi Smartphone</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">PWA Web Push Notification</p>
                    </div>
                </div>
                <button type="button" onclick="closeNotificationModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <!-- Status Badge Container -->
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Status di HP ini:</span>
                    <span id="pushStatusBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                        Memeriksa...
                    </span>
                </div>
                <p id="pushStatusDescription" class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Notifikasi web push memungkinkan pesan siaran kampus, izin, dan peringatan darurat masuk langsung ke layar smartphone Anda.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-2 pt-1">
                <button type="button" id="btnSubscribePush" onclick="handleSubscribePush()" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 active:scale-[0.99] text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center justify-center gap-2">
                    <i class="bi bi-bell"></i>
                    <span>Aktifkan Notifikasi di HP Ini</span>
                </button>

                <button type="button" id="btnTestPush" onclick="handleSendTestPush()" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.99] text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                    <i class="bi bi-send-check-fill"></i>
                    <span>Kirim Notifikasi Uji Coba Sekarang</span>
                </button>
            </div>

            <!-- Feedback Log Alert -->
            <div id="pushFeedbackBox" class="hidden p-3 rounded-xl text-xs flex items-start gap-2"></div>

            <!-- Guide Note for Android & iOS -->
            <div class="text-[11px] text-slate-500 dark:text-slate-400 border-t border-slate-100 dark:border-slate-800 pt-3 space-y-1">
                <div class="font-bold text-slate-700 dark:text-slate-300">Petunjuk Khusus Smartphone:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    <li><strong>Android (Chrome):</strong> Tekan "Aktifkan" lalu pilih <em>"Izinkan" (Allow)</em> saat pop-up browser muncul.</li>
                    <li><strong>iPhone / iOS:</strong> Wajib pasang aplikasi lewat menu <em>Share $\rightarrow$ Add to Home Screen</em> terlebih dahulu (syarat resmi iOS 16.4+).</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal Pemberitahuan Pengembangan (Kurikulum) -->
    <div id="devNoticeModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden transition-opacity">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 w-full max-w-sm space-y-4 shadow-2xl text-center">
            <div class="h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-500/20 border-2 border-blue-200 dark:border-blue-500/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-3xl mx-auto shadow-inner">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            
            <div class="space-y-1.5">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Modul Kurikulum</h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed pt-1">
                    Fitur ini <strong class="text-slate-900 dark:text-white">masih dalam pengembangan</strong>.
                </p>
            </div>

            <div class="pt-2">
                <button type="button" onclick="closeDevelopmentNotice()" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 font-bold text-xs text-white shadow-lg shadow-blue-600/30 transition">
                    Mengerti & Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white/60 dark:bg-slate-900/60 py-4 text-center text-xs text-slate-500 dark:text-slate-400 transition-colors">
        &copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · SIAP PL Deltamas
    </footer>

    <!-- Main App Scripts & Push Notification Engine -->
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

        function toggleNavDrawer() {
            const drawer = document.getElementById('navDrawer');
            const backdrop = document.getElementById('navDrawerBackdrop');
            if (!drawer || !backdrop) return;

            const isClosed = drawer.classList.contains('translate-x-full');
            if (isClosed) {
                drawer.classList.remove('translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100');
            } else {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                backdrop.classList.remove('opacity-100');
            }
        }

        function showDevelopmentNotice() {
            const modal = document.getElementById('devNoticeModal');
            if (modal) modal.classList.remove('hidden');
            const drawer = document.getElementById('navDrawer');
            const backdrop = document.getElementById('navDrawerBackdrop');
            if (drawer && !drawer.classList.contains('translate-x-full')) {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                backdrop.classList.remove('opacity-100');
            }
        }

        function closeDevelopmentNotice() {
            const modal = document.getElementById('devNoticeModal');
            if (modal) modal.classList.add('hidden');
        }

        function openNotificationModal() {
            const modal = document.getElementById('pushNotificationModal');
            if (modal) modal.classList.remove('hidden');
            checkNotificationStatus();
            const drawer = document.getElementById('navDrawer');
            const backdrop = document.getElementById('navDrawerBackdrop');
            if (drawer && !drawer.classList.contains('translate-x-full')) {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                backdrop.classList.remove('opacity-100');
            }
        }

        function closeNotificationModal() {
            const modal = document.getElementById('pushNotificationModal');
            if (modal) modal.classList.add('hidden');
        }

        // Web Push VAPID Helpers
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');
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
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-600';
                }
                if (desc) desc.textContent = 'Browser ini tidak mendukung Web Push Notification.';
                if (btnSubscribe) btnSubscribe.disabled = true;
                return;
            }

            if (Notification.permission === 'granted') {
                if (badge) {
                    badge.textContent = 'Aktif & Terhubung';
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30';
                }
                if (desc) desc.textContent = 'Smartphone Anda sudah siap menerima notifikasi instan dari kampus Pangudi Luhur.';
                if (dot) dot.classList.remove('hidden');
                if (drawerTag) {
                    drawerTag.textContent = 'Aktif';
                    drawerTag.className = 'text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold';
                }
                if (btnSubscribe) {
                    btnSubscribe.textContent = 'Sinkronkan Ulang Perangkat Ini';
                    btnSubscribe.className = 'w-full py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 font-bold text-xs transition border border-slate-300 dark:border-slate-700';
                }
            } else if (Notification.permission === 'denied') {
                if (badge) {
                    badge.textContent = 'Diblokir';
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30';
                }
                if (desc) desc.textContent = 'Izin notifikasi diblokir pada browser HP ini. Buka ikon gembok/setelan situs di browser untuk mengizinkan notifikasi.';
                if (dot) dot.classList.add('hidden');
            } else {
                if (badge) {
                    badge.textContent = 'Belum Aktif';
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30';
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
                        feedback.className = 'p-3 rounded-xl text-xs bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 flex items-center gap-2';
                        feedback.innerHTML = '<i class="bi bi-x-circle-fill"></i><span>Izin notifikasi ditolak oleh pengguna.</span>';
                        feedback.classList.remove('hidden');
                    }
                    checkNotificationStatus();
                    if (btn) btn.disabled = false;
                    return;
                }

                const registration = await navigator.serviceWorker.ready;
                
                // Fetch public key
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

                // Send to backend
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const saveRes = await fetch('{{ route("push.subscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(subJson)
                });

                const resData = await saveRes.json();
                
                if (feedback) {
                    feedback.className = 'p-3 rounded-xl text-xs bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 flex items-center gap-2';
                    feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>Smartphone Anda berhasil didaftarkan untuk menerima notifikasi!</span>';
                    feedback.classList.remove('hidden');
                }

                checkNotificationStatus();
            } catch (err) {
                console.error('Push registration error:', err);
                if (feedback) {
                    feedback.className = 'p-3 rounded-xl text-xs bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 flex items-center gap-2';
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

                // Pastikan izin aktif
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
                        title: '🔔 SIAP PL Deltamas (Uji Coba)',
                        body: 'Pemberitahuan berhasil masuk ke smartphone Anda pada ' + new Date().toLocaleTimeString('id-ID') + ' WIB.',
                        url: '{{ route("dashboard") }}'
                    })
                });

                const data = await res.json();

                if (data.success && data.sent > 0) {
                    if (feedback) {
                        feedback.className = 'p-3 rounded-xl text-xs bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 flex items-center gap-2';
                        feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>Notifikasi uji coba terkirim! Cek status bar atau layar kunci smartphone Anda.</span>';
                        feedback.classList.remove('hidden');
                    }
                } else {
                    // Fallback local notification jika belum tersimpan di DB
                    if (Notification.permission === 'granted') {
                        const reg = await navigator.serviceWorker.ready;
                        reg.showNotification('🔔 SIAP PL Deltamas (Lokal)', {
                            body: 'Notifikasi instan aktif di browser smartphone Anda.',
                            icon: '/icons/icon-192x192.png',
                            badge: '/icons/icon-192x192.png',
                            vibrate: [200, 100, 200]
                        });
                    }
                    if (feedback) {
                        feedback.className = 'p-3 rounded-xl text-xs bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-800 dark:text-amber-300 flex items-center gap-2';
                        feedback.innerHTML = '<i class="bi bi-info-circle-fill"></i><span>' + (data.message || 'Notifikasi lokal dipicu langsung di browser.') + '</span>';
                        feedback.classList.remove('hidden');
                    }
                }
            } catch (err) {
                console.error('Send test push error:', err);
                if (feedback) {
                    feedback.className = 'p-3 rounded-xl text-xs bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 flex items-center gap-2';
                    feedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i><span>Gagal mengirim notifikasi: ' + err.message + '</span>';
                    feedback.classList.remove('hidden');
                }
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        // Register Service Worker & check status on load
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then((reg) => {
                    checkNotificationStatus();
                }).catch(() => {});
            });
        }
    </script>
</body>
</html>
