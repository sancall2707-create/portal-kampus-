@extends('layouts.app')

@section('title', 'Detail Perizinan — SIAP PL Deltamas')

@section('content')
<div class="w-full px-4 py-6 sm:px-6 sm:py-8 lg:px-8 space-y-7">

    <!-- Header Back -->
    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
        <a href="{{ route('leaves.index') }}" class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Daftar Izin</span>
        </a>
        <div class="flex items-center space-x-2">
            @if($leave->status === 'approved')
                <span class="px-2.5 py-0.5 rounded-full text-xs uppercase font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                    Disetujui Resmi
                </span>
            @elseif($leave->status === 'pending_unit')
                <span class="px-2.5 py-0.5 rounded-full text-xs uppercase font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                    Menunggu Validasi Kepala Sekolah
                </span>
            @elseif($leave->status === 'pending_coordinator')
                <span class="px-2.5 py-0.5 rounded-full text-xs uppercase font-bold bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">
                    Menunggu Pengesahan Koordinator
                </span>
            @elseif($leave->status === 'rejected')
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

    <!-- Card 1: Informasi Permohonan -->
    <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-6 sm:p-8 space-y-6 shadow-sm dark:shadow-xl transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div>
                <span class="text-xs uppercase font-bold text-blue-600 dark:text-blue-400 tracking-wider">Permohonan Izin Pegawai</span>
                <h1 class="font-display text-2xl sm:text-3xl font-semibold text-slate-900 dark:text-white mt-1">{{ $leave->applicant_name }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">NIP: {{ $leave->applicant_nip ?? '-' }} · Unit {{ $leave->unit_code }} ({{ $leave->applicant_title }})</p>
            </div>
            <div class="text-left sm:text-right font-mono text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 shrink-0">
                <div>Diajukan: <span class="text-slate-900 dark:text-white font-semibold">{{ \Carbon\Carbon::parse($leave->submitted_at)->translatedFormat('d M Y, H:i') }} WIB</span></div>
                <div>Durasi: <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $leave->total_days }} Hari Kerja</span></div>
            </div>
        </div>

        <!-- Detail Data -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-xs text-slate-700 dark:text-slate-300">
            <div class="space-y-1">
                <span class="text-slate-400 uppercase tracking-wider text-[11px] font-semibold">Jenis Izin</span>
                <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $leave->leave_type_name }}</div>
            </div>
            <div class="space-y-1 font-mono">
                <span class="text-slate-400 uppercase tracking-wider text-[11px] font-semibold">Rentang Tanggal</span>
                <div class="text-sm font-bold text-slate-900 dark:text-white">
                    {{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M Y') }} s.d. {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') }}
                </div>
            </div>
        </div>

        <div class="space-y-1 text-xs">
            <span class="text-slate-400 uppercase tracking-wider text-[11px] font-semibold">Alasan Ketidakhadiran</span>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 text-sm leading-relaxed text-slate-800 dark:text-slate-200">
                {{ $leave->reason }}
            </div>
        </div>

        @if($leave->substitute_plan)
            <div class="space-y-1 text-xs">
                <span class="text-slate-400 uppercase tracking-wider text-[11px] font-semibold">Rencana KBM / Titipan Tugas Guru Pengganti (Inval)</span>
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 text-sm leading-relaxed text-slate-800 dark:text-slate-200 font-mono">
                    {{ $leave->substitute_plan }}
                </div>
            </div>
        @endif

        @if($attachment)
            <div class="pt-2">
                <span class="text-slate-400 uppercase tracking-wider text-[11px] font-semibold block mb-2">Lampiran Berkas Pendukung</span>
                <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 text-xs font-semibold text-blue-600 dark:text-blue-300 transition">
                    <i class="bi bi-paperclip text-sm"></i>
                    <span>Lihat Berkas: {{ $attachment->original_name }}</span>
                    <i class="bi bi-box-arrow-up-right text-[10px] text-slate-400"></i>
                </a>
            </div>
        @endif
    </div>

    <!-- Card 2: Status Guru Pengganti (Inval - LV-05) -->
    @if($substitute)
        <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-6 space-y-4 shadow-sm dark:shadow-xl transition-colors">
            <h3 class="text-xs uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider flex items-center gap-1.5">
                <i class="bi bi-person-lines-fill text-blue-600 dark:text-blue-400 text-sm"></i>
                <span>Penugasan Guru Pengganti (Inval) (LV-05)</span>
            </h3>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                <div class="space-y-1">
                    <div class="text-xs text-slate-400">Guru yang Ditugaskan:</div>
                    <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $substitute->substitute_name }}</div>
                    @if($substitute->class_subject_info)
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $substitute->class_subject_info }}</div>
                    @endif
                </div>

                <div class="flex items-center space-x-3">
                    @if($substitute->status === 'acknowledged')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-mono text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Tugas Telah Dikonfirmasi</span>
                        </span>
                    @else
                        @if($user->id === $substitute->substitute_user_id)
                            <form action="{{ route('leaves.acknowledge_substitute', $substitute->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                                    <i class="bi bi-check-lg"></i>
                                    <span>Konfirmasi Terima Tugas Inval</span>
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-amber-600 dark:text-amber-400 font-mono bg-amber-50 dark:bg-amber-500/10 px-3 py-1 rounded-lg border border-amber-200 dark:border-amber-500/30">
                                Menunggu Konfirmasi Guru
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Card 3: Jejak Persetujuan Berjenjang (Tiered Approval Log - Bagian 6) -->
    <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-6 sm:p-8 space-y-4 shadow-sm dark:shadow-xl transition-colors">
        <h3 class="text-xs uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider flex items-center gap-1.5">
            <i class="bi bi-diagram-3-fill text-purple-600 dark:text-purple-400 text-sm"></i>
            <span>Alur Persetujuan Berjenjang (Tiered Approval)</span>
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
                                {{ $ap->step_role === 'unit_head' ? 'Langkah 1: Validasi Kepala Sekolah Unit' : 'Langkah 2: Pengesahan Akhir Koordinator Kampus' }}
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                @if($ap->approver_name)
                                    Oleh: <strong class="text-slate-700 dark:text-slate-300">{{ $ap->approver_name }}</strong>
                                    @if($ap->delegation_reason)
                                        <span class="text-purple-600 dark:text-purple-400 font-mono">(via Plt: {{ $ap->delegation_reason }})</span>
                                    @endif
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
                                Disetujui
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

    <!-- Action Forms (Hanya tampil bagi yang berwenang) -->
    @if($canApproveUnitHead)
        <div class="rounded-3xl border border-blue-200 dark:border-blue-500/30 bg-blue-50/50 dark:bg-blue-950/20 p-6 space-y-4">
            <h3 class="text-xs uppercase font-bold text-blue-700 dark:text-blue-300 tracking-wider flex items-center gap-1.5">
                <i class="bi bi-pen-fill"></i>
                <span>Tindakan Validasi Kepala Sekolah (Langkah 1)</span>
            </h3>

            <form action="{{ route('leaves.approve_unit', $leave->id) }}" method="POST" class="space-y-4">
                @csrf

                <!-- Penunjukan Guru Inval (Opsional) -->
                @if($availableTeachers->isNotEmpty())
                    <div class="space-y-1">
                        <label for="substitute_user_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Tunjuk Guru Pengganti (Inval) (LV-05)
                        </label>
                        <select name="substitute_user_id" id="substitute_user_id" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Tanpa Guru Pengganti / Tidak Perlu Inval --</option>
                            @foreach($availableTeachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->job_title }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label for="class_subject_info" class="block text-slate-500 dark:text-slate-400 mb-1">Kelas & Mata Pelajaran Inval</label>
                            <input type="text" name="class_subject_info" id="class_subject_info" placeholder="Contoh: Kelas 3A, Jam ke 1-3" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label for="instructions" class="block text-slate-500 dark:text-slate-400 mb-1">Instruksi Tambahan Kepsek</label>
                            <input type="text" name="instructions" id="instructions" placeholder="Contoh: Awasi pengerjaan LKS hal 24" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        </div>
                    </div>
                @endif

                <div class="space-y-1">
                    <label for="note" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Catatan Kepala Sekolah (Opsional)
                    </label>
                    <input type="text" name="note" id="note" placeholder="Catatan rekomendasi..." class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>

                <div class="flex items-center space-x-3 pt-2">
                    <button type="submit" name="decision" value="approve" class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5">
                        <i class="bi bi-check-lg"></i>
                        <span>Validasi & Teruskan ke Koordinator</span>
                    </button>
                    <button type="submit" name="decision" value="reject" class="py-3 px-4 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-500/40 font-bold text-xs transition">
                        Tolak Izin
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($canApproveCoordinator)
        <div class="rounded-3xl border border-purple-200 dark:border-purple-500/30 bg-purple-50/50 dark:bg-purple-950/20 p-6 space-y-4">
            <h3 class="text-xs uppercase font-bold text-purple-700 dark:text-purple-300 tracking-wider flex items-center gap-1.5">
                <i class="bi bi-check2-circle"></i>
                <span>Tindakan Pengesahan Akhir Koordinator (Final Approval)</span>
            </h3>

            <form action="{{ route('leaves.approve_coordinator', $leave->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label for="coord_note" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Catatan Koordinator Kampus (Opsional)
                    </label>
                    <input type="text" name="note" id="coord_note" placeholder="Catatan pengesahan akhir..." class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white">
                </div>

                <div class="flex items-center space-x-3 pt-2">
                    <button type="submit" name="decision" value="approve" class="flex-1 py-3 px-4 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5">
                        <i class="bi bi-check2-all"></i>
                        <span>Mengesahkan Resmi Izin</span>
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
