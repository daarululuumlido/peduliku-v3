<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AlamatController extends Controller
{
    /**
     * Display a listing of alamat.
     */
    public function index(Request $request): Response
    {
        $query = Alamat::with(['desa.district.city.province'])
            ->withCount(['orang']);

        // Search by complete address or village name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('alamat_lengkap', 'like', "%{$search}%")
                    ->orWhereHas('desa', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $alamat = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Alamat/Index', [
            'alamat' => $alamat,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new alamat.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Alamat/Create');
    }

    /**
     * Store a newly created alamat in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'alamat_lengkap' => ['nullable', 'string'],
            'desa_id' => ['nullable', 'string', 'exists:indonesia_villages,code'],
        ]);

        Alamat::create($validated);

        return redirect()->route('admin.alamat.index')
            ->with('message', 'Data alamat berhasil ditambahkan.');
    }

    /**
     * Display the specified alamat.
     */
    public function show(Alamat $alamat): Response
    {
        $alamat->load(['desa.district.city.province']);

        return Inertia::render('Admin/Alamat/Show', [
            'alamat' => $alamat,
        ]);
    }

    /**
     * Show the form for editing the specified alamat.
     */
    public function edit(Alamat $alamat): Response
    {
        $alamat->load(['desa.district.city.province']);

        return Inertia::render('Admin/Alamat/Edit', [
            'alamat' => $alamat,
        ]);
    }

    /**
     * Update the specified alamat in storage.
     */
    public function update(Request $request, Alamat $alamat)
    {
        $validated = $request->validate([
            'alamat_lengkap' => ['nullable', 'string'],
            'desa_id' => ['nullable', 'string', 'exists:indonesia_villages,code'],
        ]);

        $alamat->update($validated);

        return redirect()->route('admin.alamat.index')
            ->with('message', 'Data alamat berhasil diperbarui.');
    }

    /**
     * Remove the specified alamat from storage.
     */
    public function destroy(Alamat $alamat)
    {
        $alamat->delete();

        return redirect()->route('admin.alamat.index')
            ->with('message', 'Data alamat berhasil dihapus.');
    }

    /**
     * Search alamat for autocomplete.
     */
    public function search(Request $request)
    {
        $search = $request->get('query');

        $results = Alamat::with(['desa.district.city.province'])
            ->where('alamat_lengkap', 'like', "%{$search}%")
            ->orWhereHas('desa', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();

        // Format for frontend
        $formatted = $results->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->full_address,
                'desa_id' => $item->desa_id,
                'alamat_lengkap' => $item->alamat_lengkap,
                'preview' => "{$item->alamat_lengkap}, ".($item->desa->name ?? '').', '.($item->desa->district->city->name ?? ''),
            ];
        });

        return response()->json($formatted);
    }
}
