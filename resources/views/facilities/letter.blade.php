<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Izin Fasilitas — {{ $booking->booking_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .print-border-none { border: none !important; shadow: none !important; }
            @page { size: A4 portrait; margin: 15mm 20mm 15mm 20mm; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen py-6 px-4">

    <!-- Action Toolbar (Hidden on Print) -->
    <div class="max-w-3xl mx-auto mb-6 no-print flex items-center justify-between">
        <a href="{{ route('facilities.show', $booking->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-sm transition">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Detail Reservasi</span>
        </a>
        <div class="flex items-center space-x-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition">
                <i class="bi bi-printer-fill"></i>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Paper Document Container (A4 Standard) -->
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-xl border border-slate-200 print-border-none space-y-6">

        <!-- Kop Surat Resmi Yayasan -->
        <div class="border-b-4 border-double border-slate-900 pb-4">
            <div class="flex items-center gap-5">
                <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo" class="h-20 w-20 object-contain shrink-0">
                <div class="text-center flex-1">
                    <h2 class="text-sm font-bold tracking-wider text-slate-700 uppercase">YAYASAN PANGUDI LUHUR PUSAT SEMARANG</h2>
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-950 uppercase tracking-tight">KAMPUS PANGUDI LUHUR KOTA DELTAMAS</h1>
                    <p class="text-[11px] text-slate-600 font-medium">Jenjang: PG-TK · SD · SMP · SMA Pangudi Luhur Bernardus</p>
                    <p class="text-[10px] text-slate-600 dark:text-slate-300 font-normal mt-0.5">
                        Jl. Deltamas Boulevard No. 1, Hegarmukti, Cikarang Pusat, Kabupaten Bekasi 17530<br>
                        Telepon: (021) 8997-2707 · Email: sekretariat@pangudiluhur-deltamas.sch.id
                    </p>
                </div>
            </div>
        </div>

        <!-- Judul Surat & Nomor Registrasi -->
        <div class="text-center space-y-1">
            <h3 class="text-base font-extrabold text-slate-900 underline uppercase tracking-wide">
                SURAT KETERANGAN IZIN PENGGUNAAN SARANA DAN PRASARANA
            </h3>
            <p class="text-xs font-mono font-bold text-slate-700">
                Nomor: {{ $booking->letter_number ?? '001/SIPF/YPL-DLT/XI/2026' }}
            </p>
        </div>

        <!-- Pernyataan Pembuka -->
        <div class="text-xs text-slate-800 leading-relaxed text-justify space-y-3">
            <p>
                Yang bertanda tangan di bawah ini, Koordinator Sarana Prasarana Sekolah Yayasan Pangudi Luhur Perwakilan Kota Deltamas, setelah memeriksa dokumen permohonan dan ketersediaan fasilitas, dengan ini menerangkan bahwa:
            </p>

            <!-- Data Pemohon -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-2 font-mono text-[11px]">
                <div>Nama Pemohon: <strong class="text-slate-900 block font-sans font-bold text-xs">{{ $booking->requester_name }}</strong></div>
                <div>NIP / Identitas: <strong class="text-slate-900 block">{{ $booking->requester_nip ?? '-' }}</strong></div>
                <div>Unit / Instansi: <strong class="text-slate-900 block font-sans font-semibold">{{ $booking->unit_name ?? ($booking->organization_name ?? 'Kampus PL Deltamas') }}</strong></div>
                <div>PIC Lapangan: <strong class="text-slate-900 block font-sans font-semibold">{{ $booking->pic_name }} (WA: {{ $booking->pic_phone }})</strong></div>
            </div>

            <p>
                Diberikan izin resmi untuk menggunakan sarana dan prasarana kampus dengan ketentuan teknis sebagai berikut:
            </p>

            <!-- Rincian Peminjaman -->
            <table class="w-full text-xs border border-slate-300 border-collapse">
                <tbody class="divide-y divide-slate-200">
                    <tr class="bg-slate-50">
                        <td class="py-2 px-3 font-semibold w-1/3 text-slate-600">Sarpras / Fasilitas</td>
                        <td class="py-2 px-3 font-bold text-slate-900">{{ $booking->facility_name }} (Kategori: {{ $booking->category_name }})</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-semibold text-slate-600">Jadwal Penggunaan</td>
                        <td class="py-2 px-3 font-mono font-bold text-slate-900">
                            {{ \Carbon\Carbon::parse($booking->start_at)->translatedFormat('l, d F Y') }}<br>
                            Pukul {{ \Carbon\Carbon::parse($booking->start_at)->format('H:i') }} s.d. {{ \Carbon\Carbon::parse($booking->end_at)->format('H:i') }} WIB
                        </td>
                    </tr>
                    <tr class="bg-slate-50">
                        <td class="py-2 px-3 font-semibold text-slate-600">Nama Acara / Kegiatan</td>
                        <td class="py-2 px-3 font-bold text-slate-900">{{ $booking->event_name ?? $booking->facility_name }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-semibold text-slate-600">Tujuan & Rincian</td>
                        <td class="py-2 px-3 text-slate-800">{{ $booking->purpose }}</td>
                    </tr>
                    <tr class="bg-slate-50">
                        <td class="py-2 px-3 font-semibold text-slate-600">Estimasi Peserta</td>
                        <td class="py-2 px-3 text-slate-900 font-mono">{{ $booking->participants ? "{$booking->participants} orang" : '-' }}</td>
                    </tr>
                    @if(strtolower($booking->category_name) === 'kendaraan')
                        <tr>
                            <td class="py-2 px-3 font-semibold text-slate-600">Rute / Tujuan Dinas</td>
                            <td class="py-2 px-3 font-bold text-slate-900">{{ $booking->destination ?? 'Dinas Luar Terlampir' }}</td>
                        </tr>
                        <tr class="bg-slate-50">
                            <td class="py-2 px-3 font-semibold text-slate-600">Supir Resmi Bertugas</td>
                            <td class="py-2 px-3 font-bold text-slate-900">{{ $booking->driver_name ?? 'Mengemudi Mandiri' }}</td>
                        </tr>
                    @endif
                    @if($items->isNotEmpty())
                        <tr>
                            <td class="py-2 px-3 font-semibold text-slate-600">Peralatan Tambahan</td>
                            <td class="py-2 px-3 font-medium text-slate-900">
                                @foreach($items as $it)
                                    <span class="inline-block bg-slate-100 px-2 py-0.5 rounded text-[11px] font-mono mr-1 mb-1 border border-slate-200">
                                        {{ $it->name }}: <strong>{{ $it->quantity }} unit</strong>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- Tata Tertib & Ketentuan -->
            <div class="text-[11px] text-slate-600 space-y-1 pt-1">
                <strong class="text-slate-800 block">Ketentuan dan Tata Tertib:</strong>
                <ol class="list-decimal list-inside space-y-0.5">
                    <li>Pemohon wajib menjaga kebersihan, ketertiban, dan keutuhan fasilitas serta peralatan sarpras yang dipinjam.</li>
                    <li>Surat izin ini wajib ditunjukkan kepada Petugas Satpam di pos gerbang kampus sebelum kegiatan dimulai.</li>
                    <li>Segala bentuk kerusakan aset wajib dilaporkan ke bagian sarpras kampus selambat-lambatnya 1x24 jam pasca pemakaian.</li>
                </ol>
            </div>
        </div>

        <!-- Kolom Pengesahan Tanda Tangan & QR Code Dinamis Satpam -->
        <div class="pt-4 border-t border-slate-200">
            <div class="flex items-center justify-between text-xs">
                
                <!-- QR Code Validasi Satpam -->
                <div class="text-center p-3 border border-slate-200 rounded-2xl bg-slate-50 shrink-0">
                    <div class="h-20 w-20 bg-slate-900 text-white rounded-xl flex flex-col items-center justify-center p-2 mx-auto">
                        <i class="bi bi-qr-code text-3xl text-emerald-400"></i>
                    </div>
                    <span class="text-[9px] font-mono block mt-1 text-slate-600 dark:text-slate-300 font-bold uppercase">Verifikasi Satpam</span>
                    <span class="text-[8px] font-mono text-slate-600 dark:text-slate-400">{{ substr($booking->booking_code, 0, 10) }}</span>
                </div>

                <!-- Tanda Tangan Koordinator Sarpras -->
                <div class="text-right space-y-1">
                    <p class="text-[11px] text-slate-600">
                        Cikarang Pusat, {{ \Carbon\Carbon::parse($booking->approved_at ?? now())->translatedFormat('d F Y') }}
                    </p>
                    <p class="font-bold text-slate-900">
                        Kordinator Sarana Prasarana Yayasan
                    </p>
                    
                    <!-- Stempel Digital Resmi & TTD -->
                    <div class="py-3">
                        <span class="inline-block px-3 py-1 rounded-lg border-2 border-emerald-600 text-emerald-700 font-extrabold text-[11px] tracking-wider uppercase -rotate-3">
                            <i class="bi bi-shield-check"></i> DISAHKAN SECARA DIGITAL
                        </span>
                    </div>

                    <p class="font-extrabold text-slate-950 underline text-sm">
                        {{ $coordinatorApproval->approver_name ?? 'Br. Stefanus Ngadenin, FIC' }}
                    </p>
                    <p class="text-[10px] text-slate-600 dark:text-slate-300 font-mono">
                        Koordinator Sekolah PL Deltamas
                    </p>
                </div>

            </div>
        </div>

    </div>

</body>
</html>
