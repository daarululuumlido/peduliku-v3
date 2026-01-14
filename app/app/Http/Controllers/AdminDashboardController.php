<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\Orang;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): Response
    {
        $user = request()->user();

        // Prepare stats based on role
        $stats = $this->getStatsForUser($user);

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
        ]);
    }

    /**
     * Get dashboard stats based on user role.
     */
    protected function getStatsForUser($user): array
    {
        // Super Admin sees everything
        if ($user->hasRole('Super Admin')) {
            return [
                'totalOrang' => Orang::count(),
                'totalKK' => KartuKeluarga::count(),
                'totalUsers' => User::count(),
                'recentOrang' => Orang::latest()->take(5)->get(['id', 'nama', 'nik', 'created_at']),
            ];
        }

        // Staff HR sees people-focused stats
        if ($user->hasRole('Staff HR')) {
            return [
                'totalOrang' => Orang::count(),
                'totalKK' => KartuKeluarga::count(),
                'recentOrang' => Orang::latest()->take(5)->get(['id', 'nama', 'nik', 'created_at']),
            ];
        }

        // Default/Viewer sees summary only
        return [
            'totalOrang' => Orang::count(),
            'totalKK' => KartuKeluarga::count(),
        ];
    }
}
