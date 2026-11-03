<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Keterbacaan Siaran — SIAP PL Deltamas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top App Bar -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('announcements.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 transition">
                    <span>&larr; Kembali ke Siaran</span>
                </a>
            </div>
            <div class="text-xs font-semibold text-slate-300">
                Laporan Keterbacaan (Read Receipts)
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full space-y-6">

        <!-- Announcement Summary Card -->
        <div class="rounded-2xl border {{ $announcement->is_urgent ? 'border-rose-500/40 bg-rose-950/20' : 'border-slate-800 bg-slate-800/50' }} p-6 space-y-3 shadow-xl backdrop-blur">
            <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                @if($announcement->scope === 'campus')
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        Kampus PL Deltamas
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                        Unit {{ $announcement->unit_code }}
                    </span>
                @endif

                @if($announcement->is_urgent)
                    <span class="px-2 py-0.5 rounded-md text-[10px] uppercase font-bold bg-rose-500/30 text-rose-300 border border-rose-500/40">
                        PENTING
                    </span>
                @endif
                <span class="text-xs text-slate-400 font-mono">Disiarkan {{ \Carbon\Carbon::parse($announcement->publish_at)->translatedFormat('d M Y, H:i') }} WIB</span>
            </div>

            <div class="text-base text-white font-medium leading-relaxed">
                "{{ $announcement->body }}"
            </div>

            <div class="text-xs text-slate-400 pt-1 border-t border-slate-800/80">
                Pengirim: <strong class="text-slate-300">{{ $announcement->sender_name }}</strong> ({{ $announcement->sender_title }})
            </div>
        </div>

        <!-- Metrics Cards (COM-02) -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="rounded-xl border border-slate-800 bg-slate-800/40 p-4 space-y-1">
                <div class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Total Target</div>
                <div class="text-2xl font-bold text-white font-mono">{{ $totalRecipients }} <span class="text-xs text-slate-500">Pegawai</span></div>
            </div>
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 space-y-1">
                <div class="text-[11px] uppercase tracking-wider text-emerald-400 font-semibold">Sudah Membaca</div>
                <div class="text-2xl font-bold text-emerald-300 font-mono">{{ $readCount }} <span class="text-xs text-emerald-400">Pegawai</span></div>
            </div>
            <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 space-y-1">
                <div class="text-[11px] uppercase tracking-wider text-amber-400 font-semibold">Belum Membaca</div>
                <div class="text-2xl font-bold text-amber-300 font-mono">{{ $unreadCount }} <span class="text-xs text-amber-400">Pegawai</span></div>
            </div>
            <div class="rounded-xl border border-blue-500/30 bg-blue-500/10 p-4 space-y-1">
                <div class="text-[11px] uppercase tracking-wider text-blue-400 font-semibold">Rasio Keterbacaan</div>
                <div class="text-2xl font-bold text-blue-300 font-mono">{{ $readPercentage }}%</div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-emerald-400 h-2.5 rounded-full transition-all duration-500" style="width: {{ $readPercentage }}%"></div>
        </div>

        <!-- Detailed Recipients Table -->
        <div class="space-y-3">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                Rincian Status Baca Per Pegawai
            </h3>

            <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-800/40 shadow-xl backdrop-blur">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-800/80 text-slate-300 uppercase tracking-wider text-[11px] border-b border-slate-700 font-semibold">
                        <tr>
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">NIP</th>
                            <th class="py-3 px-4">Nama Pegawai & Jabatan</th>
                            <th class="py-3 px-4">Unit Penempatan</th>
                            <th class="py-3 px-4">Status Keterbacaan</th>
                            <th class="py-3 px-4">Waktu Dibaca</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @foreach($recipients as $idx => $rec)
                            <tr class="hover:bg-slate-800/60 transition {{ $rec->read_at ? '' : 'bg-amber-950/10' }}">
                                <td class="py-3 px-4 font-mono text-slate-500">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-white">{{ $rec->nip ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-white">{{ $rec->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $rec->job_title ?? 'Staf' }}</div>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-300">
                                    {{ $rec->units_list ?? 'Kampus' }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($rec->read_at)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            <span>Sudah Dibaca</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            <span>Belum Dibaca</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono text-xs {{ $rec->read_at ? 'text-slate-300' : 'text-slate-500' }}">
                                    @if($rec->read_at)
                                        {{ \Carbon\Carbon::parse($rec->read_at)->translatedFormat('d M Y, H:i:s') }} WIB
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-900/60 py-4 text-center text-xs text-slate-500">
        &copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · SIAP PL Deltamas
    </footer>

</body>
</html>
