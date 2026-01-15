<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\KartuKeluarga;
use App\Models\Orang;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class OrangController extends Controller
{
    public function __construct(protected DocumentService $documentService) {}

    /**
     * Display a listing of orang.
     */
    public function index(Request $request): Response
    {
        $query = Orang::with(['alamatKtp.desa.district.city.province', 'kartuKeluargaAnggota.kartuKeluarga']);

        // Search by name or NIK
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter by gender
        if ($request->has('gender') && $request->gender) {
            $query->where('gender', $request->gender);
        }

        // Sort
        $sortField = $request->get('sort', 'nama');
        $sortDirection = $request->get('direction', 'asc');
        
        $validSortFields = ['nama', 'nik', 'gender', 'tanggal_lahir', 'created_at'];
        
        if (in_array($sortField, $validSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('nama', 'asc');
        }

        $orang = $query->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/People/Index', [
            'orang' => $orang,
            'filters' => $request->only(['search', 'gender', 'sort', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new orang.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/People/Create');
    }

    /**
     * Store a newly created orang in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', 'unique:orang,nik'],
            'nama' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['required', 'date'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'nama_ibu_kandung' => ['nullable', 'string', 'max:255'],
            'no_whatsapp' => ['nullable', 'string', 'max:20'],
            'alamat_id' => ['nullable', 'exists:alamat,id'],
            'alamat_lengkap' => ['nullable', 'required_without:alamat_id', 'string'],
            'desa_id' => ['nullable', 'required_without:alamat_id', 'string', 'exists:indonesia_villages,code'],
            'kartu_keluarga_id' => ['nullable', 'exists:kartu_keluarga,id'],
            'status_hubungan' => ['nullable', 'in:'.implode(',', array_keys(\App\Models\KartuKeluargaAnggota::getStatusHubunganOptions()))],
            'new_no_kk' => ['nullable', 'string', 'size:16', 'unique:kartu_keluarga,no_kk'],
            'dokumen.*' => ['nullable', 'file', 'max:10240'], // Max 10MB per file
        ]);

        // Determine alamat_id
        $alamatId = $validated['alamat_id'] ?? null;

        // If no existing address selected, create new if details provided
        if (! $alamatId && ($validated['desa_id'] || $validated['alamat_lengkap'])) {
            $alamat = Alamat::create([
                'desa_id' => $validated['desa_id'] ?? null,
                'alamat_lengkap' => $validated['alamat_lengkap'] ?? null,
            ]);
            $alamatId = $alamat->id;
        }

        // Handle Kartu Keluarga
        $kkId = $validated['kartu_keluarga_id'] ?? null;
        if (! $kkId && ! empty($validated['new_no_kk'])) {
            $kk = KartuKeluarga::create([
                'no_kk' => $validated['new_no_kk'],
                'alamat_id' => $alamatId, // Default new KK address to person's address
            ]);
            $kkId = $kk->id;
        }

        // Create orang
        $orang = Orang::create([
            'nik' => $validated['nik'],
            'nama' => $validated['nama'],
            'gender' => $validated['gender'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'nama_ibu_kandung' => $validated['nama_ibu_kandung'] ?? null,
            'no_whatsapp' => $validated['no_whatsapp'] ?? null,
            'alamat_ktp_id' => $alamatId,
        ]);

        // Handle Kartu Keluarga relationship if provided
        if ($kkId && ! empty($validated['status_hubungan'])) {
            \App\Models\KartuKeluargaAnggota::create([
                'kartu_keluarga_id' => $kkId,
                'orang_id' => $orang->id,
                'status_hubungan' => $validated['status_hubungan'],
            ]);
        }

        // Handle document uploads
        if ($request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $file) {
                $this->documentService->upload($file, $orang);
            }
        }

        return redirect()->route('admin.orang.index')
            ->with('message', 'Data orang berhasil ditambahkan.');
    }

    /**
     * Display the specified orang.
     */
    public function show(Orang $orang): Response
    {
        $orang->load(['alamatKtp.desa.district.city.province', 'kartuKeluargaAnggota.kartuKeluarga.alamat', 'dokumen']);

        return Inertia::render('Admin/People/Show', [
            'orang' => $orang,
        ]);
    }

    /**
     * Show the form for editing the specified orang.
     */
    public function edit(Orang $orang): Response
    {
        $orang->load(['alamatKtp.desa.district.city.province', 'kartuKeluargaAnggota.kartuKeluarga']);

        return Inertia::render('Admin/People/Edit', [
            'orang' => $orang,
        ]);
    }

    /**
     * Update the specified orang in storage.
     */
    public function update(Request $request, Orang $orang)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', Rule::unique('orang', 'nik')->ignore($orang->id)],
            'nama' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['required', 'date'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'nama_ibu_kandung' => ['nullable', 'string', 'max:255'],
            'no_whatsapp' => ['nullable', 'string', 'max:20'],
            'alamat_id' => ['nullable', 'exists:alamat,id'],
            'alamat_lengkap' => ['nullable', 'required_without:alamat_id', 'string'],
            'desa_id' => ['nullable', 'required_without:alamat_id', 'string', 'exists:indonesia_villages,code'],
            'kartu_keluarga_id' => ['nullable', 'exists:kartu_keluarga,id'],
            'status_hubungan' => ['nullable', 'in:'.implode(',', array_keys(\App\Models\KartuKeluargaAnggota::getStatusHubunganOptions()))],
            'new_no_kk' => ['nullable', 'string', 'size:16', 'unique:kartu_keluarga,no_kk'],
            'dokumen.*' => ['nullable', 'file', 'max:10240'], // Max 10MB per file
        ]);

        // Handle address update
        if (isset($validated['alamat_id']) && $validated['alamat_id']) {
            // User selected an existing address
            $orang->alamat_ktp_id = $validated['alamat_id'];
        } elseif ($validated['desa_id'] || $validated['alamat_lengkap']) {
            $alamat = Alamat::create([
                'desa_id' => $validated['desa_id'] ?? null,
                'alamat_lengkap' => $validated['alamat_lengkap'] ?? null,
            ]);
            $orang->alamat_ktp_id = $alamat->id;
        }

        // Handle KK relationship update
        $kkId = null;
        if (isset($validated['kartu_keluarga_id']) && $validated['kartu_keluarga_id']) {
            $kkId = $validated['kartu_keluarga_id'];
        } elseif (! empty($validated['new_no_kk'])) {
            $kk = KartuKeluarga::create([
                'no_kk' => $validated['new_no_kk'],
                'alamat_id' => $orang->alamat_ktp_id, // Default to person's address
            ]);
            $kkId = $kk->id;
        }

        // Update KartuKeluargaAnggota relationship
        if ($kkId && ! empty($validated['status_hubungan'])) {
            \App\Models\KartuKeluargaAnggota::updateOrCreate(
                [
                    'orang_id' => $orang->id,
                    'kartu_keluarga_id' => $kkId,
                ],
                [
                    'status_hubungan' => $validated['status_hubungan'],
                ]
            );
        } elseif (isset($kkId) && $kkId === null) {
            // Remove all KK relationships if expressly set to null
            \App\Models\KartuKeluargaAnggota::where('orang_id', $orang->id)->delete();
        }

        $orang->update([
            'nik' => $validated['nik'],
            'nama' => $validated['nama'],
            'gender' => $validated['gender'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'nama_ibu_kandung' => $validated['nama_ibu_kandung'] ?? null,
            'no_whatsapp' => $validated['no_whatsapp'] ?? null,
            'alamat_ktp_id' => $orang->alamat_ktp_id,
        ]);

        // Handle document uploads
        if ($request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $file) {
                $this->documentService->upload($file, $orang);
            }
        }

        return redirect()->route('admin.orang.index')
            ->with('message', 'Data orang berhasil diperbarui.');
    }

    /**
     * Remove the specified orang from storage (soft delete).
     */
    public function destroy(Orang $orang)
    {
        $orang->delete();

        return redirect()->route('admin.orang.index')
            ->with('message', 'Data orang berhasil dihapus.');
    }

    /**
     * Display a listing of trashed orang.
     */
    public function trashed(): Response
    {
        $orang = Orang::onlyTrashed()
            ->with(['alamatKtp'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/People/Trashed', [
            'orang' => $orang,
        ]);
    }

    /**
     * Restore a trashed orang.
     */
    public function restore(string $id)
    {
        $orang = Orang::onlyTrashed()->findOrFail($id);
        $orang->restore();

        return redirect()->route('admin.orang.trashed')
            ->with('message', 'Data orang berhasil dipulihkan.');
    }

    /**
     * Permanently delete a trashed orang.
     */
    public function forceDelete(string $id)
    {
        $orang = Orang::onlyTrashed()->findOrFail($id);
        $orang->forceDelete();

        return redirect()->route('admin.orang.trashed')
            ->with('message', 'Data orang berhasil dihapus permanen.');
    }

    /**
     * Search orang for autocomplete.
     */
    public function search(Request $request)
    {
        $search = $request->get('query', '');

        $results = Orang::where('nama', 'like', "%{$search}%")
            ->orWhere('nik', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'nama', 'nik']);

        return response()->json($results);
    }
}
