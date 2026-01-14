<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\KartuKeluarga;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class KartuKeluargaController extends Controller
{
    /**
     * Display a listing of kartu keluarga.
     */
    public function index(Request $request): Response
    {
        $query = KartuKeluarga::with(['alamat.desa.district.city.province', 'anggota']);

        // Search by no_kk
        if ($request->has('search') && $request->search) {
            $query->where('no_kk', 'like', "%{$request->search}%");
        }

        $kartuKeluarga = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/KartuKeluarga/Index', [
            'kartuKeluarga' => $kartuKeluarga,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new kartu keluarga.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/KartuKeluarga/Create');
    }

    /**
     * Store a newly created kartu keluarga in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_kk' => ['required', 'string', 'size:16', 'unique:kartu_keluarga,no_kk'],
            'alamat_id' => ['nullable', 'exists:alamat,id'],
            'alamat_lengkap' => ['nullable', 'required_without:alamat_id', 'string'],
            'desa_id' => ['nullable', 'required_without:alamat_id', 'string', 'exists:indonesia_villages,code'],
        ]);

        // Determine alamat_id
        $alamatId = $validated['alamat_id'] ?? null;

        // If no existing address selected, create new if details provided
        if (!$alamatId && ($validated['desa_id'] || $validated['alamat_lengkap'])) {
            $alamat = Alamat::create([
                'desa_id' => $validated['desa_id'] ?? null,
                'alamat_lengkap' => $validated['alamat_lengkap'] ?? null,
            ]);
            $alamatId = $alamat->id;
        }

        KartuKeluarga::create([
            'no_kk' => $validated['no_kk'],
            'alamat_id' => $alamatId,
        ]);

        return redirect()->route('admin.kartu-keluarga.index')
            ->with('message', 'Kartu Keluarga berhasil ditambahkan.');
    }

    /**
     * Display the specified kartu keluarga.
     */
    public function show(KartuKeluarga $kartuKeluarga): Response
    {
        $kartuKeluarga->load(['alamat.desa.district.city.province', 'anggota']);

        return Inertia::render('Admin/KartuKeluarga/Show', [
            'kartuKeluarga' => $kartuKeluarga,
        ]);
    }

    /**
     * Show the form for editing the specified kartu keluarga.
     */
    public function edit(KartuKeluarga $kartuKeluarga): Response
    {
        $kartuKeluarga->load(['alamat.desa.district.city.province']);

        return Inertia::render('Admin/KartuKeluarga/Edit', [
            'kartuKeluarga' => $kartuKeluarga,
        ]);
    }

    /**
     * Update the specified kartu keluarga in storage.
     */
    public function update(Request $request, KartuKeluarga $kartuKeluarga)
    {
        $validated = $request->validate([
            'no_kk' => ['required', 'string', 'size:16', Rule::unique('kartu_keluarga', 'no_kk')->ignore($kartuKeluarga->id)],
            'alamat_id' => ['nullable', 'exists:alamat,id'],
            'alamat_lengkap' => ['nullable', 'required_without:alamat_id', 'string'],
            'desa_id' => ['nullable', 'required_without:alamat_id', 'string', 'exists:indonesia_villages,code'],
        ]);

        // Handle address update
        if (isset($validated['alamat_id']) && $validated['alamat_id']) {
            // User selected an existing address
            $kartuKeluarga->alamat_id = $validated['alamat_id'];
        } elseif ($validated['desa_id'] || $validated['alamat_lengkap']) {
            // Create new address
            $alamat = Alamat::create([
                'desa_id' => $validated['desa_id'] ?? null,
                'alamat_lengkap' => $validated['alamat_lengkap'] ?? null,
            ]);
            $kartuKeluarga->alamat_id = $alamat->id;
        }

        $kartuKeluarga->update([
            'no_kk' => $validated['no_kk'],
            // alamat_id is set above if changed, otherwise it stays same (unless model update needs it explicitly if dirty)
            'alamat_id' => $kartuKeluarga->alamat_id,
        ]);

        return redirect()->route('admin.kartu-keluarga.index')
            ->with('message', 'Kartu Keluarga berhasil diperbarui.');
    }

    /**
     * Remove the specified kartu keluarga from storage.
     */
    public function destroy(KartuKeluarga $kartuKeluarga)
    {
        // Check if KK has members
        if ($kartuKeluarga->anggota()->count() > 0) {
            return redirect()->route('admin.kartu-keluarga.index')
                ->with('error', 'Tidak dapat menghapus KK yang masih memiliki anggota.');
        }

        $kartuKeluarga->delete();

        return redirect()->route('admin.kartu-keluarga.index')
            ->with('message', 'Kartu Keluarga berhasil dihapus.');
    }

    /**
     * Add member to kartu keluarga.
     */
    public function addMember(Request $request, KartuKeluarga $kartuKeluarga)
    {
        $validated = $request->validate([
            'orang_id' => ['required', 'exists:orang,id'],
        ]);

        // Update orang's kartu_keluarga_id
        $kartuKeluarga->anggota()->where('id', $validated['orang_id'])->update([
            'kartu_keluarga_id' => $kartuKeluarga->id,
        ]);

        return redirect()->route('admin.kartu-keluarga.show', $kartuKeluarga)
            ->with('message', 'Anggota berhasil ditambahkan.');
    }

    /**
     * Remove member from kartu keluarga.
     */
    public function removeMember(Request $request, KartuKeluarga $kartuKeluarga)
    {
        $validated = $request->validate([
            'orang_id' => ['required', 'exists:orang,id'],
        ]);

        // Remove orang from kartu keluarga
        $kartuKeluarga->anggota()->where('id', $validated['orang_id'])->update([
            'kartu_keluarga_id' => null,
        ]);

        return redirect()->route('admin.kartu-keluarga.show', $kartuKeluarga)
            ->with('message', 'Anggota berhasil dihapus dari KK.');
    }

    /**
     * Search kartu keluarga for autocomplete.
     */
    public function search(Request $request)
    {
        $search = $request->get('query');

        $query = KartuKeluarga::with(['alamat.desa.district.city.province', 'kepalaKeluarga'])
            ->where('no_kk', 'like', "%{$search}%");
        
        // Optional: Also search by head of family name
        $query->orWhereHas('kepalaKeluarga', function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%");
        });

        $results = $query->limit(10)->get();

        $formatted = $results->map(function ($item) {
            $kepala = $item->kepalaKeluarga ? " - " . $item->kepalaKeluarga->nama : "";
            $alamat = $item->alamat ? $item->alamat->full_address : "Alamat tidak tersedia";
            
            return [
                'id' => $item->id,
                'no_kk' => $item->no_kk,
                'text' => $item->no_kk . $kepala,
                'preview' => $alamat,
                'alamat_id' => $item->alamat_id, // Useful to inherit address
            ];
        });

        return response()->json($formatted);
    }
}
