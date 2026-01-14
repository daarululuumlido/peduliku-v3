<?php

namespace App\Http\Middleware;

use App\Services\ModuleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleAccess
{
    public function __construct(protected ModuleService $moduleService)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->moduleService->isModuleAccessible($module, $user)) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        return $next($request);
    }
}
