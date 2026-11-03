@extends('layouts.app')

@section('title', 'Detail Reservasi — SIAP PL Deltamas')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header Back -->
    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
        <a href="{{ route('facilities.index') }}" class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Fasilitas</span>
        </a>
        <div class="flex items-center space-x-2">
            @if($booking->status === 'approved')
                <span class="px-2.5 py-0.5 rounded-full text-xs uppercase font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                    Disetujui Resmi
                </span>
            @elseif($booking->status === 'pending_screening')
                <span class="px-2.5 py-0.5 rounded-full text-xs uppercase font-bold bg-cyan-100 dark:bg-cyan-500/20 text-cyan-800 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-500/30">
                    Screening TU
                </span>
            @elseif($booking->status === 'pending_unit')
                <span class="px-2.5 py-0.5 rounded-full text-xs uppercase font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                    Menunggu Validasi Kepsek
                </span>
            @elseif($booking->status === 'pending_coordinator')
                <span class="px-2.5 py-0.5 rounded-full text-xs uppercase font-bold bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">
                    Menunggu Koordinator
                </span>
            @elseif($booking->status === 'rejected')
                <span class="px-2.5 py-0.5 rounded-full text-xs uppercase font-bold bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                    Ditolak
                </span>
            @endif
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Card 1: E-Ticket QR (Tampil jika sudah disetujui - FC-09) -->
    @if($booking->status === 'approved')
        <div class="rounded-3xl border-2 border-emerald-400 dark:border-emerald-500/60 bg-gradient-to-r from-emerald-950/30 via-slate-900 to-slate-900 p-6 sm:p-8 space-y-4 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-emerald-500/30 pb-4">
                <div class="space-y-1">
                    <span class="px-2.5 py-0.5 rounded text-[10px] uppercase font-bold bg-emerald-500 text-slate-950">E-Ticket Sah Kampus</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Izin Reservasi Fasilitas Disetujui</h2>
                    <p class="text-xs text-slate-300">Tunjukkan tiket digital ini kepada Petugas Satpam saat memasuki gerbang dan menggunakan fasilitas.</p>
                </div>
                <div class="text-center sm:text-right">
                    <div class="p-3 bg-white rounded-2xl inline-block shadow-md">
                        <!-- Visual simulated QR code box -->
                        <div class="h-24 w-24 bg-slate-900 rounded-lg flex flex-col items-center justify-center text-white p-2">
                            <i class="bi bi-qr-code text-4xl text-emerald-400"></i>
                            <span class="text-[9px] font-mono mt-1 text-slate-300">{{ substr($booking->booking_code, 0, 8) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono text-slate-300 pt-1">
                <div>Kode Booking: <strong class="text-white block text-sm">{{ $booking->booking_code }}</strong></div>
                <div>Fasilitas: <strong class="text-emerald-400 block text-sm">{{ $booking->facility_name }}</strong></div>
                <div>Tanggal: <strong class="text-white block">{{ \Carbon\Carbon::parse($booking->start_at)->translatedFormat('d M Y') }}</strong></div>
                <div>Jam: <strong class="text-white block">{{ \Carbon\Carbon::parse($booking->start_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_at)->format('H:i') }} WIB</strong></div>
            </div>
        </div>
    @endif

    <!-- Card 2: Informasi Detail Pengajuan -->
    <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-6 sm:p-8 space-y-6 shadow-sm dark:shadow-xl transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div>
                <span class="text-xs uppercase font-bold text-blue-600 dark:text-blue-400 tracking-wider">Detail Permohonan Fasilitas</span>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $booking->facility_name }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">Kategori: {{ $booking->category_name }} · Kode: {{ $booking->booking_code }}</p>
            </div>
            <div class="text-left sm:text-right font-mono text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 shrink-0">
                <div>Pemohon: <span class="text-slate-900 dark:text-white font-bold">{{ $booking->requester_name }}</span></div>
                <div>Instansi: <span class="text-slate-700 dark:text-slate-300">{{ $booking->organization_name ?? 'Internal Sekolah' }}</span></div>
                <div>Telepon: <span class="text-slate-700 dark:text-slate-300">{{ $booking->requester_phone ?? '-' }}</span></div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-xs text-slate-700 dark:text-slate-300 font-mono">
            <div class="space-y-1">
                <span class="text-slate-400 uppercase tracking-wider text-[11px] font-semibold">Jadwal Penggunaan</span>
                <div class="text-sm font-bold text-slate-900 dark:text-white">
                    {{ \Carbon\Carbon::parse($booking->start_at)->translatedFormat('d M Y, H:i') }} s.d. {{ \Carbon\Carbon::parse($booking->end_at)->translatedFormat('H:i') }} WIB
                </div>
            </div>
            <div class="space-y-1">
                <span class="text-slate-400 uppercase tracking-wider text-[11px] font-semibold">Estimasi Peserta</span>
                <div class="text-sm font-bold text-slate-900 dark:text-white">
                    {{ $booking->participants ? "{$booking->participants} Orang" : '- (Tidak diisi)' }}
                </div>
            </div>
        </div>

        <div class="space-y-1 text-xs">
            <span class="text-slate-400 uppercase tracking-wider text-[11px] font-semibold">Tujuan Acara / Kegiatan</span>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 text-sm leading-relaxed text-slate-800 dark:text-slate-200">
                {{ $booking->purpose }}
            </div>
        </div>

        @if($attachment)
            <div class="pt-2">
                <span class="text-slate-400 uppercase tracking-wider text-[11px] font-semibold block mb-2">Berkas Permohonan Resmi</span>
                <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 text-xs font-semibold text-blue-600 dark:text-blue-300 transition">
                    <i class="bi bi-file-earmark-pdf text-sm"></i>
                    <span>Lihat Dokumen: {{ $attachment->original_name }}</span>
                    <i class="bi bi-box-arrow-up-right text-[10px] text-slate-400"></i>
                </a>
            </div>
        @endif
    </div>

    <!-- Card 3: Alur Persetujuan Berjenjang (Approvals Log) -->
    <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-6 sm:p-8 space-y-4 shadow-sm dark:shadow-xl transition-colors">
        <h3 class="text-xs uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider flex items-center gap-1.5">
            <i class="bi bi-diagram-3-fill text-purple-600 dark:text-purple-400 text-sm"></i>
            <span>Jejak Persetujuan Berjenjang Fasilitas</span>
        </h3>

        <div class="space-y-3">
            @foreach($approvals as $ap)
                <div class="p-4 rounded-2xl border {{ $ap->status === 'approved' ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-500/30' : ($ap->status === 'rejected' ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-500/30' : 'bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800') }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <span class="h-8 w-8 rounded-xl font-mono font-bold text-xs flex items-center justify-center {{ $ap->status === 'approved' ? 'bg-emerald-600 text-white' : ($ap->status === 'rejected' ? 'bg-rose-600 text-white' : 'bg-slate-300 dark:bg-slate-800 text-slate-700 dark:text-slate-400') }}">
                            {{ $ap->step_order }}
                        </span>
                        <div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">
                                @if($ap->step_role === 'tu_screening')
                                    Screening Kelengkapan Dokumen (Tata Usaha Kampus)
                                @elseif($ap->step_role === 'unit_head')
                                    Validasi Kepala Sekolah Unit
                                @else
                                    Pengesahan Akhir Koordinator Kampus
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                @if($ap->approver_name)
                                    Oleh: <strong class="text-slate-700 dark:text-slate-300">{{ $ap->approver_name }}</strong>
                                @else
                                    Menunggu tindakan pejabat berwenang
                                @endif
                            </div>
                            @if($ap->note)
                                <div class="text-[11px] text-slate-600 dark:text-slate-300 italic mt-0.5">Catatan: "{{ $ap->note }}"</div>
                            @endif
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        @if($ap->status === 'approved')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono text-emerald-800 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/30">
                                Lolos / Setuju
                            </span>
                        @elseif($ap->status === 'rejected')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono text-rose-800 dark:text-rose-300 bg-rose-100 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30">
                                Ditolak
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-500/20 border border-amber-200 dark:border-amber-500/30">
                                Menunggu
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Action Forms Sesuai Hak Akses -->
    @if($canScreenTU)
        <div class="rounded-3xl border border-cyan-200 dark:border-cyan-500/30 bg-cyan-50/50 dark:bg-cyan-950/20 p-6 space-y-4">
            <h3 class="text-xs uppercase font-bold text-cyan-700 dark:text-cyan-300 tracking-wider flex items-center gap-1.5">
                <i class="bi bi-file-earmark-check-fill"></i>
                <span>Tindakan Screening Dokumen Pemohon Eksternal (Tata Usaha)</span>
            </h3>

            <form action="{{ route('facilities.action_screening', $booking->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="tu_note" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Catatan Screening TU
                    </label>
                    <input type="text" name="note" id="tu_note" placeholder="Contoh: Dokumen lengkap / lampiran KTP penanggung jawab sah" class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>

                <div class="flex items-center space-x-3 pt-2">
                    <button type="submit" name="decision" value="pass" class="flex-1 py-3 px-4 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5">
                        <i class="bi bi-check-lg"></i>
                        <span>Loloskan Screening & Teruskan</span>
                    </button>
                    <button type="submit" name="decision" value="return" class="py-3 px-4 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-500/40 font-bold text-xs transition">
                        Kembalikan / Tolak
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($canApproveUnit)
        <div class="rounded-3xl border border-amber-200 dark:border-amber-500/30 bg-amber-50/50 dark:bg-amber-950/20 p-6 space-y-4">
            <h3 class="text-xs uppercase font-bold text-amber-700 dark:text-amber-300 tracking-wider flex items-center gap-1.5">
                <i class="bi bi-pen-fill"></i>
                <span>Tindakan Validasi Kepala Sekolah Unit</span>
            </h3>

            <form action="{{ route('facilities.action_approve_unit', $booking->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="unit_note" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Catatan Kepala Sekolah
                    </label>
                    <input type="text" name="note" id="unit_note" placeholder="Catatan persetujuan unit..." class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>

                <div class="flex items-center space-x-3 pt-2">
                    <button type="submit" name="decision" value="approve" class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5">
                        <i class="bi bi-check-lg"></i>
                        <span>Validasi & Teruskan ke Koordinator</span>
                    </button>
                    <button type="submit" name="decision" value="reject" class="py-3 px-4 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-500/40 font-bold text-xs transition">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($canApproveCoordinator)
        <div class="rounded-3xl border border-purple-200 dark:border-purple-500/30 bg-purple-50/50 dark:bg-purple-950/20 p-6 space-y-4">
            <h3 class="text-xs uppercase font-bold text-purple-700 dark:text-purple-300 tracking-wider flex items-center gap-1.5">
                <i class="bi bi-check2-circle"></i>
                <span>Tindakan Pengesahan Akhir Koordinator Kampus (Aktifkan E-Ticket QR)</span>
            </h3>

            <form action="{{ route('facilities.action_approve_coordinator', $booking->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="coord_note" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Catatan Koordinator Kampus
                    </label>
                    <input type="text" name="note" id="coord_note" placeholder="Catatan pengesahan akhir..." class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>

                <div class="flex items-center space-x-3 pt-2">
                    <button type="submit" name="decision" value="approve" class="flex-1 py-3 px-4 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5">
                        <i class="bi bi-qr-code"></i>
                        <span>Sahkan Reservasi & Terbitkan E-Ticket</span>
                    </button>
                    <button type="submit" name="decision" value="reject" class="py-3 px-4 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-500/40 font-bold text-xs transition">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
