<?php

namespace App\Http\Controllers;

use App\Models\Orang;
use App\Models\Alamat;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class OrangController extends Controller
{
    public function __construct(protected DocumentService $documentService)
    {
    }

    /**
     * Display a listing of orang.
     */
    public function index(Request $request): Response
    {
        $query = Orang::with(['alamatKtp.desa.district.city.province', 'kartuKeluarga']);

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

        $orang = $query->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/People/Index', [
            'orang' => $orang,
            'filters' => $request->only(['search', 'gender']),
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
            'dokumen.*' => ['nullable', 'file', 'max:10240'], // Max 10MB per file
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
        $orang->load(['alamatKtp.desa.district.city.province', 'kartuKeluarga.alamat', 'dokumen']);

        return Inertia::render('Admin/People/Show', [
            'orang' => $orang,
        ]);
    }

    /**
     * Show the form for editing the specified orang.
     */
    public function edit(Orang $orang): Response
    {
        $orang->load(['alamatKtp.desa.district.city.province']);

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
            'dokumen.*' => ['nullable', 'file', 'max:10240'], // Max 10MB per file
        ]);

        // Handle address update
        if (isset($validated['alamat_id']) && $validated['alamat_id']) {
            // User selected an existing address
            $orang->alamat_ktp_id = $validated['alamat_id'];
        } elseif ($validated['desa_id'] || $validated['alamat_lengkap']) {
            // User entered new address details
            // Always create new if explicitly inputting details (assuming intent is to use specific new details)
            // Or update existing if linked?
            // "Buat Alamat Baru" implies creating new. 
            // If user was editing an existing linked address, they should edit that address directly or create new.
            // Let's create new to be safe and avoid mutating shared addresses inadvertently, 
            // unless we want to "Edit current address". 
            // Given the requirement "cari alamat dan bila tidak ada ada tombol buat alamat baru", 
            // it implies switching to a new address.
            
            $alamat = Alamat::create([
                'desa_id' => $validated['desa_id'] ?? null,
                'alamat_lengkap' => $validated['alamat_lengkap'] ?? null,
            ]);
            $orang->alamat_ktp_id = $alamat->id;
        }
        // If neither, keep existing or handle removal? 
        // For now, if fields are empty and no alamat_id, it might mean keeping current or clearing. 
        // But validation `required_without:alamat_id` enforces at least one path if we strictly follow it.
        // However, since they are nullable, let's just save.

        $orang->update([
            'nik' => $validated['nik'],
            'nama' => $validated['nama'],
            'gender' => $validated['gender'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'nama_ibu_kandung' => $validated['nama_ibu_kandung'] ?? null,
            'no_whatsapp' => $validated['no_whatsapp'] ?? null,
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
}
