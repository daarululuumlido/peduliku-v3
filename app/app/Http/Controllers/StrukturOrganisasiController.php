<?php

namespace App\Http\Controllers;

use App\Models\StrukturOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StrukturOrganisasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $strukturs = StrukturOrganisasi::withCount('unitOrganisasi as units_count')
            ->when($request->query('search'), function ($query, $search) {
                $query->where('nama_periode', 'like', "%{$search}%");
            })
            ->when($request->query('status'), function ($query, $status) {
                $query->where('is_active', $status === '1');
            })
            ->orderBy('tgl_mulai', 'desc')
            ->paginate(20);

        return inertia('Hris/StrukturOrganisasi/Index', [
            'strukturs' => $strukturs,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Hris/StrukturOrganisasi/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date|after:tgl_mulai',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // If this is set as active, deactivate all other periods
            if ($validated['is_active'] ?? false) {
                StrukturOrganisasi::query()->update(['is_active' => false]);
            }

            $struktur = StrukturOrganisasi::create($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Struktur organisasi berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal membuat struktur organisasi: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StrukturOrganisasi $strukturOrganisasi)
    {
        $strukturOrganisasi->load(['unitOrganisasi' => function ($query) {
            $query->withCount('masterJabatan as jabatan_count')
                ->with(['parent', 'children' => function ($q) {
                    $q->withCount('masterJabatan as jabatan_count');
                }])
                ->orderBy('level_hierarki')
                ->orderBy('urutan');
        }]);

        // Count total pegawai in this structure
        $pegawaiCount = DB::table('peran_pegawai')
            ->join('histori_jabatan_pegawai', 'peran_pegawai.id', '=', 'histori_jabatan_pegawai.peran_pegawai_id')
            ->join('master_jabatan', 'histori_jabatan_pegawai.master_jabatan_id', '=', 'master_jabatan.id')
            ->join('unit_organisasi', 'master_jabatan.unit_organisasi_id', '=', 'unit_organisasi.id')
            ->where('unit_organisasi.struktur_id', $strukturOrganisasi->id)
            ->where('peran_pegawai.is_active', true)
            ->whereNull('histori_jabatan_pegawai.tgl_selesai')
            ->count();

        $strukturOrganisasi->pegawai_count = $pegawaiCount;

        // Get all unit IDs in this structure
        $unitIds = $strukturOrganisasi->unitOrganisasi->pluck('id');

        // Fetch jabatan grouped by unit with employee counts
        $jabatanData = DB::table('master_jabatan')
            ->select([
                'master_jabatan.id as jabatan_id',
                'master_jabatan.unit_organisasi_id as unit_id',
                'master_jabatan.nama_jabatan',
                'master_jabatan.is_pimpinan',
                DB::raw('COUNT(DISTINCT peran_pegawai.id) as pegawai_count'),
            ])
            ->leftJoin('histori_jabatan_pegawai', function ($join) {
                $join->on('master_jabatan.id', '=', 'histori_jabatan_pegawai.master_jabatan_id')
                    ->whereNull('histori_jabatan_pegawai.tgl_selesai');
            })
            ->leftJoin('peran_pegawai', function ($join) {
                $join->on('histori_jabatan_pegawai.peran_pegawai_id', '=', 'peran_pegawai.id')
                    ->where('peran_pegawai.is_active', true);
            })
            ->whereIn('master_jabatan.unit_organisasi_id', $unitIds)
            ->groupBy('master_jabatan.id', 'master_jabatan.unit_organisasi_id', 'master_jabatan.nama_jabatan', 'master_jabatan.is_pimpinan')
            ->orderBy('master_jabatan.is_pimpinan', 'desc') // Pimpinan first
            ->orderBy('master_jabatan.nama_jabatan')
            ->get()
            ->groupBy('unit_id')
            ->map(function ($jabatans) {
                return $jabatans->values()->toArray();
            })
            ->toArray();

        // Fetch pegawai grouped by jabatan (using composite key unit_id_jabatan_id)
        $pegawaiData = DB::table('peran_pegawai')
            ->join('histori_jabatan_pegawai', 'peran_pegawai.id', '=', 'histori_jabatan_pegawai.peran_pegawai_id')
            ->join('master_jabatan', 'histori_jabatan_pegawai.master_jabatan_id', '=', 'master_jabatan.id')
            ->join('unit_organisasi', 'master_jabatan.unit_organisasi_id', '=', 'unit_organisasi.id')
            ->join('orang', 'peran_pegawai.orang_id', '=', 'orang.id')
            ->where('unit_organisasi.struktur_id', $strukturOrganisasi->id)
            ->where('peran_pegawai.is_active', true)
            ->whereNull('histori_jabatan_pegawai.tgl_selesai')
            ->select([
                'unit_organisasi.id as unit_id',
                'master_jabatan.id as jabatan_id',
                'master_jabatan.is_pimpinan',
                'peran_pegawai.id as pegawai_id',
                'peran_pegawai.nip',
                'orang.nama',
                'orang.gelar_depan',
                'orang.gelar_belakang',
                'master_jabatan.nama_jabatan',
                'histori_jabatan_pegawai.tgl_mulai',
            ])
            ->orderBy('master_jabatan.is_pimpinan', 'desc') // Pimpinan positions first
            ->orderBy('orang.nama')
            ->get()
            ->groupBy(function ($item) {
                return $item->unit_id.'_'.$item->jabatan_id;
            })
            ->map(function ($employees) {
                return $employees->map(function ($emp) {
                    $nama = $emp->nama;
                    if ($emp->gelar_depan) {
                        $nama = $emp->gelar_depan . ' ' . $nama;
                    }
                    if ($emp->gelar_belakang) {
                        $nama = $nama . ', ' . $emp->gelar_belakang;
                    }
                    $emp->nama_lengkap = $nama;
                    return $emp;
                })->values()->toArray();
            })
            ->toArray();

        return inertia('Hris/StrukturOrganisasi/Show', [
            'struktur' => $strukturOrganisasi,
            'units' => $strukturOrganisasi->unitOrganisasi->map(function ($unit) {
                return $unit->toArray();
            }),
            'jabatanByUnit' => $jabatanData,
            'pegawaiByJabatan' => $pegawaiData,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StrukturOrganisasi $strukturOrganisasi)
    {
        return inertia('Hris/StrukturOrganisasi/Edit', [
            'struktur' => $strukturOrganisasi,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StrukturOrganisasi $strukturOrganisasi)
    {
        $validated = $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date|after:tgl_mulai',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // If this is set as active, deactivate all other periods
            if ($validated['is_active'] ?? false) {
                StrukturOrganisasi::where('id', '!=', $strukturOrganisasi->id)
                    ->update(['is_active' => false]);
            }

            $strukturOrganisasi->update($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Struktur organisasi berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui struktur organisasi: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StrukturOrganisasi $strukturOrganisasi)
    {
        DB::beginTransaction();

        try {
            $strukturOrganisasi->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Struktur organisasi berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus struktur organisasi: '.$e->getMessage());
        }
    }

    /**
     * Clone structure from existing period.
     */
    public function clone(Request $request, StrukturOrganisasi $strukturOrganisasi)
    {
        $validated = $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date|after:tgl_mulai',
        ]);

        DB::beginTransaction();

        try {
            // Deactivate all periods
            StrukturOrganisasi::query()->update(['is_active' => false]);

            // Create new structure
            $newStruktur = StrukturOrganisasi::create([
                'nama_periode' => $validated['nama_periode'],
                'tgl_mulai' => $validated['tgl_mulai'],
                'tgl_selesai' => $validated['tgl_selesai'] ?? null,
                'is_active' => true,
            ]);

            // Clone all units with their structure
            $strukturOrganisasi->unitOrganisasi->each(function ($unit) use ($newStruktur) {
                $newUnit = $unit->replicate();
                $newUnit->struktur_id = $newStruktur->id;
                $newUnit->save();
            });

            DB::commit();

            return redirect()->back()
                ->with('success', 'Struktur organisasi berhasil dikloning');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal kloning struktur organisasi: '.$e->getMessage());
        }
    }
}

