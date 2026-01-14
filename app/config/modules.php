<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Modules
    |--------------------------------------------------------------------------
    |
    | Define all available modules in the application. Each module has:
    | - name: Display name
    | - icon: Icon name (heroicons)
    | - route: Default route when entering module
    | - permission: Required permission to access module
    | - menu: Navigation menu items
    |
    */

    'admin' => [
        'name' => 'Admin',
        'icon' => 'cog-6-tooth',
        'route' => 'admin.dashboard',
        'permission' => 'module.admin.access',
        'menu' => [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'icon' => 'home',
                'permission' => null, // Always visible in module
            ],
            [
                'label' => 'Data Orang',
                'route' => 'admin.orang.index',
                'icon' => 'users',
                'permission' => 'orang.view',
            ],
            [
                'label' => 'Kartu Keluarga',
                'route' => 'admin.kartu-keluarga.index',
                'icon' => 'identification',
                'permission' => 'kartu_keluarga.view',
            ],
            [
                'type' => 'group',
                'label' => 'Settings',
                'icon' => 'cog',
                'permission' => 'users.view|roles.view|permissions.view',
                'items' => [
                    [
                        'label' => 'Users',
                        'route' => 'admin.settings.users.index',
                        'icon' => 'user-circle',
                        'permission' => 'users.view',
                    ],
                    [
                        'label' => 'Roles',
                        'route' => 'admin.settings.roles.index',
                        'icon' => 'shield-check',
                        'permission' => 'roles.view',
                    ],
                    [
                        'label' => 'Permissions',
                        'route' => 'admin.settings.permissions.index',
                        'icon' => 'key',
                        'permission' => 'permissions.view',
                    ],
                ],
            ],
        ],
    ],

    // Future modules can be added here:
    // 'akademik' => [...],
    // 'keuangan' => [...],
    // 'kepegawaian' => [...],

];
