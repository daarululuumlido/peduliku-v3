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

        return inertia('Hris/StrukturOrganisasi/Show', [
            'struktur' => $strukturOrganisasi,
            'units' => $strukturOrganisasi->unitOrganisasi,
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

