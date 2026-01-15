<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditController extends Controller
{
    protected $middleware = [
        'can:view auditors',
    ];

    public function index(Request $request): Response
    {
        $query = Audit::with(['user']);

        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
        }

        if ($request->has('auditable_type') && $request->auditable_type) {
            $query->where('auditable_type', $request->auditable_type);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $validSortFields = ['created_at', 'event', 'auditable_type', 'user_id'];
        
        if (in_array($sortField, $validSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $audits = $query->paginate(20)->withQueryString();

        $auditableTypes = Audit::select('auditable_type')
            ->distinct()
            ->pluck('auditable_type')
            ->map(fn ($type) => match ($type) {
                'App\Models\Orang' => 'Orang',
                'App\Models\KartuKeluarga' => 'Kartu Keluarga',
                'App\Models\KartuKeluargaAnggota' => 'Anggota KK',
                'App\Models\Alamat' => 'Alamat',
                'App\Models\User' => 'User',
                default => $type,
            });

        $events = ['created', 'updated', 'deleted', 'restored'];

        return Inertia::render('Admin/Audits/Index', [
            'audits' => $audits,
            'filters' => $request->only(['event', 'auditable_type', 'user_id', 'date_from', 'date_to', 'sort', 'direction']),
            'auditableTypes' => $auditableTypes,
            'events' => $events,
        ]);
    }

    public function show($id): Response
    {
        $audit = Audit::with(['user', 'auditable'])
            ->findOrFail($id);

        $oldValues = $audit->old_values ?? [];
        $newValues = $audit->new_values ?? [];

        return Inertia::render('Admin/Audits/Show', [
            'audit' => $audit,
            'oldValues' => $oldValues,
            'newValues' => $newValues,
        ]);
    }
}
