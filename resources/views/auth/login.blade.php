<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — SIAP PL Deltamas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 text-slate-100 min-h-screen flex flex-col justify-between antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top Small Bar -->
    <header class="w-full border-b border-slate-800/80 bg-slate-950/40 backdrop-blur">
        <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center space-x-2 text-xs text-slate-400">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                <span>Sistem Informasi & Administrasi Pegawai</span>
            </div>
            <a href="{{ route('landing') }}" class="text-xs text-blue-400 hover:text-blue-300 transition flex items-center gap-1 font-medium">
                <span>Daftar Akun Testing</span>
                <span>&rarr;</span>
            </a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-4">
        <div class="w-full max-w-md">

            <!-- Card Container -->
            <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl shadow-black/60 relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <!-- Logo & Heading -->
                <div class="text-center space-y-3 mb-7">
                    <div class="inline-flex items-center justify-center p-2 rounded-2xl bg-slate-800/80 border border-slate-700/60 shadow-inner">
                        <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo Pangudi Luhur Kota Deltamas" class="h-20 w-20 object-contain drop-shadow-md">
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-white tracking-tight">SIAP PL Deltamas</h1>
                        <p class="text-xs text-slate-400 mt-1">Yayasan Pangudi Luhur — Kampus Deltamas</p>
                    </div>
                </div>

                <!-- Alert Errors -->
                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-start space-x-2">
                        <svg class="h-4 w-4 text-rose-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
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
                        <label for="login" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
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
                                class="w-full px-4 py-3 bg-slate-950/70 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono tracking-wide transition"
                            >
                            <span class="absolute right-3 top-3 text-slate-500 text-xs">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                Kata Sandi
                            </label>
                            <span class="text-[11px] text-slate-400">Default test: <code class="text-blue-300 font-mono">password123</code></span>
                        </div>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="••••••••" 
                                required 
                                class="w-full px-4 py-3 bg-slate-950/70 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono tracking-wide transition"
                            >
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-3 text-slate-500 hover:text-slate-300 text-xs">
                                <svg id="eyeIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center space-x-2 text-xs text-slate-300 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded bg-slate-950 border-slate-700 text-blue-600 focus:ring-blue-500/20">
                            <span>Ingat sesi saya</span>
                        </label>
                        <span class="text-xs text-slate-500">Lupa sandi? Hub. TU</span>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 active:scale-[0.99] font-bold text-sm text-white shadow-lg shadow-blue-600/30 transition duration-150 flex items-center justify-center space-x-2">
                            <span>Masuk ke Portal</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Quick Fill Buttons for Testing -->
                <div class="mt-6 pt-5 border-t border-slate-800">
                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2.5 text-center">
                        Pilih Cepat Akun Pengujian
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <button type="button" onclick="fillTest('PL0001', 'password123')" class="px-2.5 py-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700/70 text-left transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-purple-300">Koordinator</div>
                                <div class="text-[10px] text-slate-400 font-mono">PL0001</div>
                            </div>
                            <span class="text-slate-500 text-xs">&rarr;</span>
                        </button>
                        <button type="button" onclick="fillTest('PL1002', 'password123')" class="px-2.5 py-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700/70 text-left transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-blue-300">Kepsek SD</div>
                                <div class="text-[10px] text-slate-400 font-mono">PL1002</div>
                            </div>
                            <span class="text-slate-500 text-xs">&rarr;</span>
                        </button>
                        <button type="button" onclick="fillTest('PL2002', 'password123')" class="px-2.5 py-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700/70 text-left transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-amber-300">Guru Multi-Unit</div>
                                <div class="text-[10px] text-slate-400 font-mono">PL2002 (SMP-SMA)</div>
                            </div>
                            <span class="text-slate-500 text-xs">&rarr;</span>
                        </button>
                        <button type="button" onclick="fillTest('PL4001', 'password123')" class="px-2.5 py-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700/70 text-left transition flex items-center justify-between">
                            <div>
                                <div class="font-bold text-rose-300">Satpam Gerbang</div>
                                <div class="text-[10px] text-slate-400 font-mono">PL4001</div>
                            </div>
                            <span class="text-slate-500 text-xs">&rarr;</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Hint below card -->
            <p class="text-center text-xs text-slate-500 mt-4">
                Memerlukan bantuan teknis? Hubungi Tim IT / Tata Usaha Kampus PL Deltamas.
            </p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-800/80 bg-slate-950/40 py-3 text-center text-xs text-slate-500">
        &copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · Dilindungi Hak Cipta
    </footer>

    <script>
        function fillTest(nip, pwd) {
            document.getElementById('login').value = nip;
            document.getElementById('password').value = pwd;
        }

        function togglePassword() {
            const pwd = document.getElementById('password');
            if (pwd.type === 'password') {
                pwd.type = 'text';
            } else {
                pwd.type = 'password';
            }
        }
    </script>
</body>
</html>
