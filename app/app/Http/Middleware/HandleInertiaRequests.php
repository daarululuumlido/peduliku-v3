<?php

namespace App\Http\Middleware;

use App\Services\ModuleService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    public function __construct(protected ModuleService $moduleService)
    {
    }

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $currentModule = $this->detectCurrentModule($request);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'modules' => fn () => $user ? $this->moduleService->getAccessibleModules($user) : [],
            'currentModule' => $currentModule,
            'moduleMenu' => fn () => $user && $currentModule 
                ? $this->moduleService->getModuleMenu($currentModule, $user) 
                : [],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }

    /**
     * Detect the current module from the route.
     */
    protected function detectCurrentModule(Request $request): ?string
    {
        $routeName = $request->route()?->getName();
        
        if (!$routeName) {
            return null;
        }

        // Extract module from route name (e.g., 'admin.dashboard' => 'admin')
        $parts = explode('.', $routeName);
        
        if (count($parts) > 0) {
            $potentialModule = $parts[0];
            
            // Check if this is a valid module
            if (config("modules.{$potentialModule}")) {
                return $potentialModule;
            }
        }

        return null;
    }
}

