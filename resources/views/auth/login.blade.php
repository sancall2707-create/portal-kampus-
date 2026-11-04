<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — SIAP PL Deltamas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
    <script>
        if (localStorage.theme === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIAP PL">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col justify-between antialiased transition-colors duration-200 selection:bg-blue-600 selection:text-white">

    <!-- Top Minimal Bar -->
    <header class="w-full border-b border-slate-200 dark:border-slate-800/80 bg-white/70 dark:bg-slate-950/40 backdrop-blur transition-colors">
        <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center space-x-2 text-xs text-slate-600 dark:text-slate-400">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                <span>Sistem Informasi & Administrasi Pegawai</span>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="toggleTheme()" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 transition" title="Ganti Tema">
                    <i id="themeIconSun" class="bi bi-sun-fill text-amber-500 hidden dark:inline"></i>
                    <i id="themeIconMoon" class="bi bi-moon-stars-fill text-slate-600 dark:hidden inline"></i>
                </button>
                <a href="{{ route('landing') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 font-medium">
                    <span>Daftar Akun Testing</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-4">
        <div class="w-full max-w-md">

            <!-- Card Container -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl dark:shadow-2xl dark:shadow-black/60 relative overflow-hidden transition-colors">

                <!-- Logo & Heading (Format Stacked Center Sesuai Panduan) -->
                <div class="flex flex-col items-center justify-center text-center mb-7">
                    <!-- Logo dengan lis & kontainer putih melingkar -->
                    <div class="h-20 w-20 rounded-full bg-white p-1.5 mb-3 shadow-md flex items-center justify-center shrink-0 border border-slate-200 dark:border-white/20">
                        <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo PL Deltamas" class="h-full w-full object-contain rounded-full" />
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">SIAP PL Deltamas</h1>
                    <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Yayasan Pangudi Luhur — Kampus Deltamas</p>
                </div>

                <!-- Alert Errors -->
                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs flex items-start space-x-2">
                        <i class="bi bi-exclamation-triangle-fill text-rose-600 dark:text-rose-400 shrink-0 mt-0.5"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="login" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            NIP Pegawai / Email
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="login" 
                                name="login" 
                                value="{{ old('login') }}" 
                                placeholder="Contoh: PL0001, PL2002" 
                                required 
                                autofocus
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono tracking-wide transition"
                            >
                            <span class="absolute right-3.5 top-3.5 text-slate-400 dark:text-slate-500 text-sm">
                                <i class="bi bi-person-badge"></i>
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Kata Sandi
                            </label>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">Default: <code class="text-blue-600 dark:text-blue-300 font-mono font-bold">password123</code></span>
                        </div>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="Password akun" 
                                required 
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono tracking-wide transition"
                            >
                            <button type="button" onclick="togglePassword()" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 text-sm">
                                <i id="eyeIcon" class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center space-x-2 text-xs text-slate-600 dark:text-slate-300 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500">
                            <span>Ingat sesi saya</span>
                        </label>
                        <span class="text-xs text-slate-400">Lupa sandi? Hub. TU</span>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 active:scale-[0.99] font-bold text-sm text-white shadow-lg shadow-blue-600/30 transition flex items-center justify-center space-x-2">
                            <span>Masuk ke Portal</span>
                            <i class="bi bi-box-arrow-in-right"></i>
                        </button>
                    </div>
                </form>

                <!-- Quick Fill Buttons -->
                <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-800">
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5 text-center">
                        Pilih Cepat Akun Pengujian
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <button type="button" onclick="fillTest('PL0001', 'password123')" class="px-2.5 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-left transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-purple-600 dark:text-purple-300">Koordinator</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">PL0001</div>
                            </div>
                            <i class="bi bi-chevron-right text-slate-400 text-xs"></i>
                        </button>
                        <button type="button" onclick="fillTest('PL1002', 'password123')" class="px-2.5 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-left transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-blue-600 dark:text-blue-300">Kepsek SD</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">PL1002</div>
                            </div>
                            <i class="bi bi-chevron-right text-slate-400 text-xs"></i>
                        </button>
                        <button type="button" onclick="fillTest('PL2002', 'password123')" class="px-2.5 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-left transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-amber-600 dark:text-amber-300">Guru Multi-Unit</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">PL2002 (SMP-SMA)</div>
                            </div>
                            <i class="bi bi-chevron-right text-slate-400 text-xs"></i>
                        </button>
                        <button type="button" onclick="fillTest('PL4001', 'password123')" class="px-2.5 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-left transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-rose-600 dark:text-rose-300">Satpam Gerbang</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">PL4001</div>
                            </div>
                            <i class="bi bi-chevron-right text-slate-400 text-xs"></i>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Footer info -->
            <p class="text-center text-xs text-slate-500 dark:text-slate-400 mt-4">
                Memerlukan bantuan teknis? Hubungi Tim IT / Tata Usaha Kampus PL Deltamas.
            </p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-200 dark:border-slate-800/80 bg-white/70 dark:bg-slate-950/40 py-3 text-center text-xs text-slate-500 dark:text-slate-400 transition-colors">
        &copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · Dilindungi Hak Cipta
    </footer>

    <script>
        function fillTest(nip, pwd) {
            document.getElementById('login').value = nip;
            document.getElementById('password').value = pwd;
        }

        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                pwd.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
