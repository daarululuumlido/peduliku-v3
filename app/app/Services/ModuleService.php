<?php

namespace App\Services;

use App\Models\User;

class ModuleService
{
    /**
     * Get all modules accessible by the user.
     */
    public function getAccessibleModules(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $modules = config('modules', []);
        $accessible = [];

        foreach ($modules as $slug => $module) {
            if ($this->isModuleAccessible($slug, $user)) {
                $accessible[$slug] = [
                    'slug' => $slug,
                    'name' => $module['name'],
                    'icon' => $module['icon'],
                    'route' => $module['route'],
                ];
            }
        }

        return $accessible;
    }

    /**
     * Check if user can access a specific module.
     */
    public function isModuleAccessible(string $moduleSlug, ?User $user): bool
    {
        if (! $user) {
            return false;
        }

        // Super Admin can access all modules
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        $module = config("modules.{$moduleSlug}");

        if (! $module) {
            return false;
        }

        $permission = $module['permission'] ?? null;

        if (! $permission) {
            return true; // No permission required
        }

        return $user->can($permission);
    }

    /**
     * Get filtered menu items for a module based on user permissions.
     */
    public function getModuleMenu(string $moduleSlug, ?User $user): array
    {
        if (! $user) {
            return [];
        }

        $module = config("modules.{$moduleSlug}");

        if (! $module || ! isset($module['menu'])) {
            return [];
        }

        return $this->filterMenuItems($module['menu'], $user);
    }

    /**
     * Recursively filter menu items based on user permissions.
     */
    protected function filterMenuItems(array $items, User $user): array
    {
        $filtered = [];

        foreach ($items as $item) {
            // Check if this is a group
            if (isset($item['type']) && $item['type'] === 'group') {
                // Check group permission (can be OR separated)
                if (! $this->checkPermission($item['permission'] ?? null, $user)) {
                    continue;
                }

                // Filter nested items
                $nestedItems = $this->filterMenuItems($item['items'] ?? [], $user);

                if (! empty($nestedItems)) {
                    $filtered[] = [
                        'type' => 'group',
                        'label' => $item['label'],
                        'icon' => $item['icon'] ?? null,
                        'items' => $nestedItems,
                    ];
                }
            } else {
                // Regular menu item
                if (! $this->checkPermission($item['permission'] ?? null, $user)) {
                    continue;
                }

                $filtered[] = [
                    'label' => $item['label'],
                    'route' => $item['route'],
                    'icon' => $item['icon'] ?? null,
                ];
            }
        }

        return $filtered;
    }

    /**
     * Check permission (supports OR operator with |).
     */
    protected function checkPermission(?string $permission, User $user): bool
    {
        if (! $permission) {
            return true;
        }

        // Super Admin bypasses all permissions
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Support OR operator
        if (str_contains($permission, '|')) {
            $permissions = explode('|', $permission);
            foreach ($permissions as $perm) {
                if ($user->can(trim($perm))) {
                    return true;
                }
            }

            return false;
        }

        return $user->can($permission);
    }

    /**
     * Get the first accessible module for the user.
     */
    public function getDefaultModule(?User $user): ?string
    {
        $modules = $this->getAccessibleModules($user);

        if (empty($modules)) {
            return null;
        }

        return array_key_first($modules);
    }
}
