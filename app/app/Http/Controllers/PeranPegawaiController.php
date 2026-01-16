<?php

namespace App\Http\Controllers;

use App\Models\Orang;
use App\Models\PeranPegawai;
use App\Models\HistoriJabatanPegawai;
use App\Models\MasterJabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PeranPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pegawai = PeranPegawai::with(['orang', 'currentJabatan.masterJabatan.unitOrganisasi'])
            ->when($request->query('status'), function ($query, $status) {
                $query->where('status_kepegawaian', $status);
            })
            ->when($request->query('search'), function ($query, $search) {
                $query->whereHas('orang', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                });
                $query->orWhere('nip', 'like', "%{$search}%");
            })
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return inertia('Hris/Pegawai/Index', [
            'pegawai' => $pegawai,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Hris/Pegawai/Create');
    }

    /**
     * Store a newly created resource in storage (activate orang as pegawai).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'orang_id' => 'required|exists:orang,id',
            'nip' => 'required|string|unique:peran_pegawai,nip',
            'tgl_bergabung' => 'required|date',
            'tmt' => 'nullable|date',
            'status_kepegawaian' => 'required|in:Guru Tetap,Guru Kontrak,Karyawan Tetap,Karyawan Kontrak,Honorer',
            'status_mukim' => 'nullable|in:TIDAK,MUKIM',
            'alamat_domisili_id' => 'nullable|exists:alamat,id',
            'is_pengajar' => 'boolean',
            'nfc_id' => 'nullable|string',
            'finger_id' => 'nullable|string',
            'email_internal' => 'nullable|email',
            'no_rekening' => 'nullable|string',
            'nuptk' => 'nullable|string',
            'foto_url' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $pegawai = PeranPegawai::create([
                'id' => Str::uuid(),
                'orang_id' => $validated['orang_id'],
                'nip' => $validated['nip'],
                'tgl_bergabung' => $validated['tgl_bergabung'],
                'tmt' => $validated['tmt'] ?? $validated['tgl_bergabung'],
                'status_kepegawaian' => $validated['status_kepegawaian'],
                'status_mukim' => $validated['status_mukim'] ?? 'TIDAK',
                'alamat_domisili_id' => $validated['alamat_domisili_id'] ?? null,
                'is_pengajar' => $validated['is_pengajar'] ?? false,
                'nfc_id' => $validated['nfc_id'] ?? null,
                'finger_id' => $validated['finger_id'] ?? null,
                'email_internal' => $validated['email_internal'] ?? null,
                'no_rekening' => $validated['no_rekening'] ?? null,
                'nuptk' => $validated['nuptk'] ?? null,
                'foto_url' => $validated['foto_url'] ?? null,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Pegawai berhasil diaktifkan');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal mengaktifkan pegawai: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PeranPegawai $peranPegawai)
    {
        $peranPegawai->load([
            'orang.riwayatPendidikan',
            'orang.kartuKeluargaAnggota.kartuKeluarga.anggota.orang',
            'alamatDomisili',
            'historiJabatan' => function ($query) {
                $query->with(['masterJabatan.unitOrganisasi'])->orderBy('tgl_mulai', 'desc');
            },
        ]);

        return inertia('Hris/Pegawai/Show', [
            'pegawai' => $peranPegawai,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PeranPegawai $peranPegawai)
    {
        $validated = $request->validate([
            'nip' => 'required|string|unique:peran_pegawai,nip,'.$peranPegawai->id,
            'tgl_bergabung' => 'required|date',
            'tmt' => 'nullable|date',
            'status_kepegawaian' => 'required|in:Guru Tetap,Guru Kontrak,Karyawan Tetap,Karyawan Kontrak,Honorer',
            'status_mukim' => 'nullable|in:TIDAK,MUKIM',
            'alamat_domisili_id' => 'nullable|exists:alamat,id',
            'is_pengajar' => 'boolean',
            'nfc_id' => 'nullable|string',
            'finger_id' => 'nullable|string',
            'email_internal' => 'nullable|email',
            'no_rekening' => 'nullable|string',
            'nuptk' => 'nullable|string',
            'foto_url' => 'nullable|string',
            'tgl_resign' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $peranPegawai->update($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Data pegawai berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui data pegawai: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PeranPegawai $peranPegawai)
    {
        DB::beginTransaction();

        try {
            // Soft delete by setting is_active to false and adding resign date
            $peranPegawai->update([
                'is_active' => false,
                'tgl_resign' => now(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Pegawai berhasil dinonaktifkan');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menonaktifkan pegawai: '.$e->getMessage());
        }
    }

    /**
     * Assign jabatan to pegawai.
     */
    public function assignJabatan(Request $request, PeranPegawai $peranPegawai)
    {
        $validated = $request->validate([
            'master_jabatan_id' => 'required|exists:master_jabatan,id',
            'spesialisasi' => 'nullable|string',
            'no_sk' => 'nullable|string',
            'tgl_mulai' => 'required|date',
            'is_jabatan_fungsional' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // End current active jabatan if any
            HistoriJabatanPegawai::where('peran_pegawai_id', $peranPegawai->id)
                ->whereNull('tgl_selesai')
                ->update([
                    'tgl_selesai' => $validated['tgl_mulai'],
                    'keterangan_mutasi' => 'Mutasi ke jabatan baru',
                ]);

            // Create new jabatan assignment
            HistoriJabatanPegawai::create([
                'id' => Str::uuid(),
                'peran_pegawai_id' => $peranPegawai->id,
                'master_jabatan_id' => $validated['master_jabatan_id'],
                'spesialisasi' => $validated['spesialisasi'] ?? null,
                'no_sk' => $validated['no_sk'] ?? null,
                'tgl_mulai' => $validated['tgl_mulai'],
                'is_jabatan_fungsional' => $validated['is_jabatan_fungsional'] ?? false,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Jabatan berhasil ditugaskan');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menugaskan jabatan: '.$e->getMessage());
        }
    }

    /**
     * Get available orang to activate as pegawai.
     */
    public function searchOrang(Request $request)
    {
        $search = $request->query('q');

        $orang = Orang::whereDoesntHave('peranPegawai', function ($query) {
                $query->where('is_active', true);
            })
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json($orang);
    }

    /**
     * Search active pegawai.
     */
    public function searchPegawai(Request $request)
    {
        $search = $request->query('q');

        $pegawai = PeranPegawai::with('orang')
            ->where('is_active', true)
            ->whereHas('orang', function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            })
            ->orWhere('nip', 'like', "%{$search}%")
            ->limit(20)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nip' => $p->nip,
                    'nama' => $p->orang->nama,
                    'foto_url' => $p->foto_url ?? $p->orang->foto_url,
                ];
            });

        return response()->json($pegawai);
    }

    /**
     * Get current jabatan of pegawai.
     */
    public function currentJabatan(PeranPegawai $peranPegawai)
    {
        $currentJabatan = HistoriJabatanPegawai::with('masterJabatan.unitOrganisasi')
            ->where('peran_pegawai_id', $peranPegawai->id)
            ->whereNull('tgl_selesai')
            ->first();

        return response()->json($currentJabatan);
    }
}
