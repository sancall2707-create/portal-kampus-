<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LetterController extends Controller
{
    /**
     * Buku register surat masuk dan keluar (OP-07).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('units');
        $userUnitIds = $user->units->pluck('id')->toArray();

        $direction = $request->get('direction', 'all'); // all, in, out
        $unitFilter = $request->get('unit', 'all');

        $query = DB::table('letters')
            ->join('units', 'units.id', '=', 'letters.unit_id')
            ->join('letter_categories', 'letter_categories.id', '=', 'letters.category_id')
            ->join('users as reg', 'reg.id', '=', 'letters.registered_by')
            ->select('letters.*', 'units.code as unit_code', 'letter_categories.name as category_name', 'reg.name as registrar_name')
            ->whereNull('letters.deleted_at');

        if ($user->hasRole(['koordinator', 'tata_usaha'])) {
            // Melihat seluruh arsip surat
        } elseif ($user->hasRole('kepala_sekolah')) {
            $query->whereIn('letters.unit_id', $userUnitIds);
        } else {
            $query->whereIn('letters.unit_id', $userUnitIds);
        }

        if ($direction !== 'all') {
            $query->where('letters.direction', $direction);
        }

        if ($unitFilter !== 'all') {
            $query->where('units.code', $unitFilter);
        }

        $letters = $query->orderByDesc('letters.letter_date')->get();
        $categories = DB::table('letter_categories')->where('is_active', true)->get();
        $units = Unit::all();

        return view('letters.index', compact('letters', 'categories', 'units', 'direction', 'unitFilter', 'user'));
    }

    /**
     * Simpan surat baru dengan penomoran urut otomatis (OP-07).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole(['tata_usaha', 'koordinator'])) abort(403);

        $validated = $request->validate([
            'direction'      => ['required', 'in:in,out'],
            'unit_id'        => ['required', 'exists:units,id'],
            'category_id'    => ['required', 'exists:letter_categories,id'],
            'letter_date'    => ['required', 'date'],
            'subject'        => ['required', 'string', 'max:255'],
            'party_name'     => ['required', 'string', 'max:150'],
            'classification' => ['required', 'in:biasa,penting,rahasia'],
            'notes'          => ['nullable', 'string'],
            'letter_file'    => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
        ]);

        $year = (int) date('Y', strtotime($validated['letter_date']));
        $unit = Unit::find($validated['unit_id']);
        $cat  = DB::table('letter_categories')->where('id', $validated['category_id'])->first();

        // Penomoran otomatis untuk surat keluar: {seq}/{code}/{unit}/{bulan_romawi}/{tahun}
        if ($validated['direction'] === 'out') {
            // Update sequence dalam lock transaksi
            $lastNumber = DB::transaction(function () use ($unit, $cat, $year) {
                $seq = DB::table('letter_sequences')
                    ->where('unit_id', $unit->id)
                    ->where('category_id', $cat->id)
                    ->where('year', $year)
                    ->lockForUpdate()
                    ->first();

                if ($seq) {
                    $next = $seq->last_number + 1;
                    DB::table('letter_sequences')->where('id', $seq->id)->update([
                        'last_number' => $next,
                        'updated_at'  => now(),
                    ]);
                } else {
                    $next = 1;
                    DB::table('letter_sequences')->insert([
                        'unit_id'     => $unit->id,
                        'category_id' => $cat->id,
                        'year'        => $year,
                        'last_number' => $next,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
                return $next;
            });

            $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $monthNum = (int) date('n', strtotime($validated['letter_date']));
            $monthRom = $romawi[$monthNum] ?? 'I';
            $seqFormatted = str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

            $letterNumber = "{$seqFormatted}/{$cat->code}/PL-{$unit->code}/{$monthRom}/{$year}";
        } else {
            // Surat masuk menggunakan nomor surat asli dari pengirim luar
            $letterNumber = $request->input('external_letter_number', 'SM-' . date('ymd') . '-' . Str::random(4));
        }

        $now = now();
        $letterId = DB::table('letters')->insertGetId([
            'direction'      => $validated['direction'],
            'unit_id'        => $validated['unit_id'],
            'category_id'    => $validated['category_id'],
            'letter_number'  => $letterNumber,
            'letter_date'    => $validated['letter_date'],
            'subject'        => $validated['subject'],
            'party_name'     => $validated['party_name'],
            'classification' => $validated['classification'],
            'registered_by'  => $user->id,
            'notes'          => $validated['notes'] ?? null,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        if ($request->hasFile('letter_file')) {
            $path = $request->file('letter_file')->store('attachments/letters', 'public');
            DB::table('attachments')->insert([
                'attachable_type' => 'letters',
                'attachable_id'   => $letterId,
                'purpose'         => 'arsip_surat',
                'disk'            => 'public',
                'path'            => $path,
                'original_name'   => $request->file('letter_file')->getClientOriginalName(),
                'mime'            => $request->file('letter_file')->getClientMimeType(),
                'size_bytes'      => $request->file('letter_file')->getSize(),
                'uploaded_by'     => $user->id,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }

        return redirect()->route('letters.index')
            ->with('success', "Surat berhasil dicatat dengan Nomor: {$letterNumber}");
    }
}
