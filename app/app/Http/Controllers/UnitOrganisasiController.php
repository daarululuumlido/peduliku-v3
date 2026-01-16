<?php

namespace App\Http\Controllers;

use App\Models\UnitOrganisasi;
use App\Models\StrukturOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitOrganisasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('hris.unit_organisasi.view');

        $strukturId = $request->query('struktur_id');

        // If no filter selected, default to active structure
        if (! $strukturId) {
            $activeStruktur = StrukturOrganisasi::active()->first();
            if ($activeStruktur) {
                $strukturId = $activeStruktur->id;
            }
        }
        
        // Get all units with their relationships
        $query = UnitOrganisasi::with(['parent', 'struktur', 'children'])
            ->withCount('masterJabatan')
            ->when($strukturId, function ($query, $strukturId) {
                $query->where('struktur_id', $strukturId);
            })
            ->orderBy('level_hierarki')
            ->orderBy('urutan');
        
        // Get only root-level units (those without parents)
        $units = $query->whereNull('parent_id')->get();

        $struktur = StrukturOrganisasi::all();

        return inertia('Hris/UnitOrganisasi/Index', [
            'units' => $units,
            'struktur' => $struktur,
            'filters' => [
                'struktur_id' => $strukturId,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('hris.unit_organisasi.create');

        $struktur = StrukturOrganisasi::all();
        $units = UnitOrganisasi::orderBy('nama_unit')->get();

        return inertia('Hris/UnitOrganisasi/Create', [
            'struktur' => $struktur,
            'units' => $units,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('hris.unit_organisasi.create');

        $validated = $request->validate([
            'struktur_id' => 'required|exists:struktur_organisasi,id',
            'parent_id' => 'nullable|exists:unit_organisasi,id',
            'nama_unit' => 'required|string|max:255',
            'kode_unit' => 'nullable|string|max:50|unique:unit_organisasi,kode_unit',
            'level_hierarki' => 'integer|min:0',
            'urutan' => 'integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Auto-calculate level if not provided
            if (! isset($validated['level_hierarki'])) {
                if ($validated['parent_id'] ?? null) {
                    $parent = UnitOrganisasi::find($validated['parent_id']);
                    $validated['level_hierarki'] = $parent ? $parent->level_hierarki + 1 : 0;
                } else {
                    $validated['level_hierarki'] = 0;
                }
            }

            // Ensure unique name within the same parent
            $exists = UnitOrganisasi::where('struktur_id', $validated['struktur_id'])
                ->where('parent_id', $validated['parent_id'])
                ->where('nama_unit', $validated['nama_unit'])
                ->exists();

            if ($exists) {
                throw new \Exception('Nama unit sudah ada di level yang sama.');
            }

            $unit = UnitOrganisasi::create($validated);

            DB::commit();

            return redirect()->route('hris.unit-organisasi.index')
                ->with('success', 'Unit organisasi berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal membuat unit organisasi: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UnitOrganisasi $unitOrganisasi)
    {
        $this->authorize('hris.unit_organisasi.view');

        $unitOrganisasi->load([
            'parent',
            'children',
            'struktur',
            'masterJabatan.historiJabatan' => function ($query) {
                $query->aktif()->with('peranPegawai.orang');
            }
        ]);

        return inertia('Hris/UnitOrganisasi/Show', [
            'unit' => $unitOrganisasi,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UnitOrganisasi $unitOrganisasi)
    {
        $this->authorize('hris.unit_organisasi.edit');

        $units = UnitOrganisasi::where('struktur_id', $unitOrganisasi->struktur_id)
            ->where('id', '!=', $unitOrganisasi->id)
            ->orderBy('nama_unit')
            ->get();

        return inertia('Hris/UnitOrganisasi/Edit', [
            'unit' => $unitOrganisasi,
            'units' => $units,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UnitOrganisasi $unitOrganisasi)
    {
        $this->authorize('hris.unit_organisasi.edit');

        $validated = $request->validate([
            'parent_id' => 'nullable|exists:unit_organisasi,id',
            'nama_unit' => 'required|string|max:255',
            'kode_unit' => 'nullable|string|max:50|unique:unit_organisasi,kode_unit,'.$unitOrganisasi->id,
            'level_hierarki' => 'integer|min:0',
            'urutan' => 'integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Prevent setting self as parent
            if (isset($validated['parent_id']) && $validated['parent_id'] === $unitOrganisasi->id) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menjadikan unit ini sebagai parent dari dirinya sendiri');
            }

            // Recalculate level if parent changed
            if (isset($validated['parent_id']) && $validated['parent_id'] != $unitOrganisasi->parent_id) {
                if ($validated['parent_id']) {
                    $parent = UnitOrganisasi::find($validated['parent_id']);
                    $validated['level_hierarki'] = $parent ? $parent->level_hierarki + 1 : 0;
                } else {
                    $validated['level_hierarki'] = 0;
                }
            }

            $unitOrganisasi->update($validated);

            DB::commit();

            return redirect()->route('hris.unit-organisasi.show', $unitOrganisasi->id)
                ->with('success', 'Unit organisasi berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal memperbarui unit organisasi: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnitOrganisasi $unitOrganisasi)
    {
        $this->authorize('hris.unit_organisasi.delete');

        DB::beginTransaction();

        try {
            // Check if unit has children
            if ($unitOrganisasi->children()->count() > 0) {
                throw new \Exception('Tidak dapat menghapus unit yang memiliki sub-unit. Hapus sub-unit terlebih dahulu.');
            }

            // Check if unit has master jabatan
            if ($unitOrganisasi->masterJabatan()->count() > 0) {
                 throw new \Exception('Tidak dapat menghapus unit yang memiliki jabatan. Hapus jabatan terlebih dahulu.');
            }

            $unitOrganisasi->delete();

            DB::commit();

            return redirect()->route('hris.unit-organisasi.index')
                ->with('success', 'Unit organisasi berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus unit organisasi: '.$e->getMessage());
        }
    }

    /**
     * Reorder units.
     */
    public function reorder(Request $request)
    {
        $this->authorize('hris.unit_organisasi.reorder');

        $validated = $request->validate([
            'units' => 'required|array',
            'units.*.id' => 'required|exists:unit_organisasi,id',
            'units.*.urutan' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['units'] as $unitData) {
                UnitOrganisasi::where('id', $unitData['id'])
                    ->update(['urutan' => $unitData['urutan']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Urutan unit berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan unit: '.$e->getMessage(),
            ], 500);
        }
    }
}

