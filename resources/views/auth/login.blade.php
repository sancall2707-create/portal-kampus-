<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — SIAP PL Deltamas</title>
    @include('partials.design-head')
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#102433">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIAP PL">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased selection:bg-blue-800 selection:text-white dark:bg-slate-950 dark:text-slate-100">
    <div class="grid min-h-screen lg:grid-cols-[minmax(0,0.9fr)_minmax(22rem,1fr)]">
        <aside class="relative hidden flex-col justify-between bg-ink-950 px-10 py-10 text-white lg:flex">
            <div class="flex items-center gap-3">
                <span class="grid h-12 w-12 place-items-center rounded-full bg-white p-1 ring-2 ring-gold-500/80">
                    <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo PL Deltamas" class="h-full w-full rounded-full object-contain" />
                </span>
                <span>
                    <span class="block font-display text-xl font-semibold">SIAP PL Deltamas</span>
                    <span class="block text-xs text-slate-600 dark:text-slate-400">Yayasan Pangudi Luhur — Kampus Deltamas</span>
                </span>
            </div>
            <div class="max-w-md space-y-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gold-400">Sistem Informasi & Administrasi Pegawai</p>
                <h1 class="font-display text-4xl leading-tight">Satu pintu untuk staf, guru, dan pimpinan kampus.</h1>
                <p class="text-sm leading-relaxed text-slate-300">Siaran, agenda, perizinan, fasilitas, operasional lapangan, dan persuratan tetap di alur yang sama.</p>
            </div>
            <p class="text-xs text-slate-600 dark:text-slate-300">&copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · Dilindungi Hak Cipta</p>
        </aside>

        <main class="flex flex-col">
            <header class="flex items-center justify-between gap-3 px-4 py-4 sm:px-8">
                <a href="{{ route('landing') }}" class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-200 lg:hidden">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-white p-0.5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-white/15">
                        <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo PL Deltamas" class="h-full w-full rounded-full object-contain" />
                    </span>
                    SIAP PL Deltamas
                </a>
                <div class="ml-auto flex items-center gap-3">
                    <a href="{{ route('landing') }}" class="text-xs font-semibold text-blue-700 hover:underline dark:text-blue-300">Daftar Akun Testing</a>
                    <button type="button" onclick="toggleTheme()" class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" title="Ganti Tema">
                        <i class="bi bi-sun-fill hidden text-amber-500 dark:inline"></i>
                        <i class="bi bi-moon-stars-fill inline dark:hidden"></i>
                    </button>
                </div>
            </header>

            <div class="flex flex-1 items-center justify-center px-4 py-6 sm:px-8">
                <div class="w-full max-w-md">
                    <div class="mb-7">
                        <h1 class="font-display text-3xl text-slate-900 dark:text-white">Masuk ke Portal</h1>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Gunakan NIP pegawai atau email yang sudah terdaftar.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-5 flex items-start gap-2 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-xs text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                            <i class="bi bi-exclamation-triangle-fill mt-0.5 shrink-0"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="login" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">NIP Pegawai / Email</label>
                            <div class="relative">
                                <input type="text" id="login" name="login" value="{{ old('login') }}" placeholder="Contoh: PL0001, PL2002" required autofocus class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-11 font-mono text-sm tracking-wide text-slate-900 placeholder-slate-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                <span class="absolute right-3.5 top-3.5 text-sm text-slate-600 dark:text-slate-400"><i class="bi bi-person-badge"></i></span>
                            </div>
                        </div>
                        <div>
                            <div class="mb-1.5 flex items-center justify-between">
                                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Kata Sandi</label>
                                <span class="text-[11px] text-slate-600 dark:text-slate-300">Default: <code class="font-mono font-bold text-blue-700 dark:text-blue-300">password123</code></span>
                            </div>
                            <div class="relative">
                                <input type="password" id="password" name="password" placeholder="Password akun" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-11 font-mono text-sm tracking-wide text-slate-900 placeholder-slate-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                <button type="button" onclick="togglePassword()" class="absolute right-3.5 top-3.5 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                                    <i id="eyeIcon" class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex cursor-pointer select-none items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                                <span>Ingat sesi saya</span>
                            </label>
                            <span class="text-xs text-slate-600 dark:text-slate-400">Lupa sandi? Hub. TU</span>
                        </div>
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-ink-900 py-3.5 text-sm font-semibold text-white transition hover:bg-ink-800">
                            <span>Masuk ke Portal</span>
                            <i class="bi bi-box-arrow-in-right"></i>
                        </button>
                    </form>

                    <div class="mt-6 border-t border-slate-200 pt-5 dark:border-slate-800">
                        <p class="mb-2.5 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Pilih Cepat Akun Pengujian</p>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <button type="button" onclick="fillTest('PL0001', 'password123')" class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-left transition hover:border-slate-300 dark:border-slate-700 dark:bg-slate-900">
                                <span>
                                    <span class="block font-semibold text-purple-700 dark:text-purple-300">Koordinator</span>
                                    <span class="font-mono text-[10px] text-slate-600 dark:text-slate-300">PL0001</span>
                                </span>
                                <i class="bi bi-chevron-right text-xs text-slate-600 dark:text-slate-400"></i>
                            </button>
                            <button type="button" onclick="fillTest('PL1002', 'password123')" class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-left transition hover:border-slate-300 dark:border-slate-700 dark:bg-slate-900">
                                <span>
                                    <span class="block font-semibold text-blue-700 dark:text-blue-300">Kepsek SD</span>
                                    <span class="font-mono text-[10px] text-slate-600 dark:text-slate-300">PL1002</span>
                                </span>
                                <i class="bi bi-chevron-right text-xs text-slate-600 dark:text-slate-400"></i>
                            </button>
                            <button type="button" onclick="fillTest('PL2002', 'password123')" class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-left transition hover:border-slate-300 dark:border-slate-700 dark:bg-slate-900">
                                <span>
                                    <span class="block font-semibold text-amber-700 dark:text-amber-300">Guru Multi-Unit</span>
                                    <span class="font-mono text-[10px] text-slate-600 dark:text-slate-300">PL2002 (SMP-SMA)</span>
                                </span>
                                <i class="bi bi-chevron-right text-xs text-slate-600 dark:text-slate-400"></i>
                            </button>
                            <button type="button" onclick="fillTest('PL4001', 'password123')" class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-left transition hover:border-slate-300 dark:border-slate-700 dark:bg-slate-900">
                                <span>
                                    <span class="block font-semibold text-rose-700 dark:text-rose-300">Satpam Gerbang</span>
                                    <span class="font-mono text-[10px] text-slate-600 dark:text-slate-300">PL4001</span>
                                </span>
                                <i class="bi bi-chevron-right text-xs text-slate-600 dark:text-slate-400"></i>
                            </button>
                        </div>
                    </div>
                    <p class="mt-6 text-center text-xs text-slate-600 dark:text-slate-300">Memerlukan bantuan teknis? Hubungi Tim IT / Tata Usaha Kampus PL Deltamas.</p>
                </div>
            </div>
        </main>
    </div>

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
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
