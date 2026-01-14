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
            'alamat_lengkap' => ['nullable', 'string'],
            'desa_id' => ['nullable', 'string', 'exists:indonesia_villages,code'],
        ]);

        // Create alamat if provided
        $alamatId = null;
        if ($validated['desa_id'] || $validated['alamat_lengkap']) {
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
            'alamat_lengkap' => ['nullable', 'string'],
            'desa_id' => ['nullable', 'string', 'exists:indonesia_villages,code'],
        ]);

        // Update or create alamat
        if ($validated['desa_id'] || $validated['alamat_lengkap']) {
            if ($kartuKeluarga->alamat_id) {
                $kartuKeluarga->alamat->update([
                    'desa_id' => $validated['desa_id'] ?? null,
                    'alamat_lengkap' => $validated['alamat_lengkap'] ?? null,
                ]);
            } else {
                $alamat = Alamat::create([
                    'desa_id' => $validated['desa_id'] ?? null,
                    'alamat_lengkap' => $validated['alamat_lengkap'] ?? null,
                ]);
                $kartuKeluarga->alamat_id = $alamat->id;
            }
        }

        $kartuKeluarga->update([
            'no_kk' => $validated['no_kk'],
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
}
