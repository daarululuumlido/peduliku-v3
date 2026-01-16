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
    public function index()
    {
        $struktur = StrukturOrganisasi::withCount('unitOrganisasi')
            ->orderBy('tgl_mulai', 'desc')
            ->get();

        return inertia('StrukturOrganisasi/Index', [
            'struktur' => $struktur,
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
            $query->with('parent')->orderBy('level_hierarki')->orderBy('urutan');
        }]);

        return inertia('StrukturOrganisasi/Show', [
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

