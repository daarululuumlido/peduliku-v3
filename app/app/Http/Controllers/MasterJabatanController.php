<?php

namespace App\Http\Controllers;

use App\Models\MasterJabatan;
use App\Models\UnitOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterJabatanController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(UnitOrganisasi $unitOrganisasi)
    {
        return inertia('Hris/MasterJabatan/Create', [
            'unit' => $unitOrganisasi,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, UnitOrganisasi $unitOrganisasi)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'is_pimpinan' => 'boolean',
            'kuota_sdm' => 'integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $unitOrganisasi->masterJabatan()->create($validated);

            DB::commit();

            return redirect()->route('hris.unit-organisasi.show', $unitOrganisasi->id)
                ->with('success', 'Jabatan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menambahkan jabatan: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UnitOrganisasi $unitOrganisasi, MasterJabatan $masterJabatan)
    {
        return inertia('Hris/MasterJabatan/Edit', [
            'unit' => $unitOrganisasi,
            'jabatan' => $masterJabatan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UnitOrganisasi $unitOrganisasi, MasterJabatan $masterJabatan)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'is_pimpinan' => 'boolean',
            'kuota_sdm' => 'integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $masterJabatan->update($validated);

            DB::commit();

            return redirect()->route('hris.unit-organisasi.show', $unitOrganisasi->id)
                ->with('success', 'Jabatan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui jabatan: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnitOrganisasi $unitOrganisasi, MasterJabatan $masterJabatan)
    {
        DB::beginTransaction();

        try {
            // Check if jabatan has dependencies (histori jabatan pegawai)
            if ($masterJabatan->historiJabatan()->count() > 0) {
                throw new \Exception('Tidak dapat menghapus jabatan yang masih digunakan oleh pegawai.');
            }

            $masterJabatan->delete();

            DB::commit();

            return redirect()->route('hris.unit-organisasi.show', $unitOrganisasi->id)
                ->with('success', 'Jabatan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus jabatan: '.$e->getMessage());
        }
    }
}
