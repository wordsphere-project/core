<?php

declare(strict_types=1);

use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use App\Models\User;

return [

    'resources' => [
        'PermissionResource' => PermissionResource::class,
        'RoleResource' => RoleResource::class,
    ],

    'preload_roles' => true,

    'preload_permissions' => true,

    'navigation_section_group' => 'filament-spatie-roles-permissions::filament-spatie.section.roles_and_permissions', // Default uses language constant

    'team_model' => null,

    'scope_to_tenant' => true,

    /*
     * Set as false to remove from navigation.
     */
    'should_register_on_navigation' => [
        'permissions' => true,
        'roles' => true,
    ],

    'guard_names' => [
        'web' => 'web',
        'api' => 'api',
    ],

    'toggleable_guard_names' => [
        'roles' => [
            'isToggledHiddenByDefault' => true,
        ],
        'permissions' => [
            'isToggledHiddenByDefault' => true,
        ],
    ],

    'default_guard_name' => null,

    'model_filter_key' => 'return \'%\'.$value;', // Eg: 'return \'%\'.$key.'\%\';'

    'user_name_column' => 'name',

    /*
     * Icons to use for navigation
     */
    'icons' => [
        'role_navigation' => 'heroicon-o-lock-closed',
        'permission_navigation' => 'heroicon-o-lock-closed',
    ],

    /*
     *  Navigation items order - int value, false  restores the default position
     */

    'sort' => [
        'role_navigation' => false,
        'permission_navigation' => false,
    ],

    'generator' => [

        'guard_names' => [
            'web',
            'api',
        ],

        'permission_affixes' => [

            /*
             * Permissions Aligned with Policies.
             * DO NOT change the keys unless the genericPolicy.stub is published and altered accordingly
             */
            'viewAnyPermission' => 'view-any',
            'viewPermission' => 'view',
            'createPermission' => 'create',
            'updatePermission' => 'update',
            'deletePermission' => 'delete',
            'restorePermission' => 'restore',
            'forceDeletePermission' => 'force-delete',

            /*
             * Additional Resource Permissions
             */
            'replicate',
            'reorder',
        ],

        /*
         * returns the "name" for the permission.
         *
         * $permission which is an iteration of [permission_affixes] ,
         * $model The model to which the $permission will be concatenated
         *
         * Eg: 'permission_name' => 'return $permissionAffix . ' ' . Str::kebab($modelName),
         *
         * Note: If you are changing the "permission_name" , It's recommended to run with --clean to avoid duplications
         */
        'permission_name' => 'return $permissionAffix . \' \' . $modelName;',

        /*
         * Permissions will be generated for the models associated with the respective Filament Resources
         */
        'discover_models_through_filament_resources' => false,

        /*
         * Include directories which consists of models.
         */
        'model_directories' => [
            app_path('Models'),
            //app_path('Domains/Forum')
        ],

        /*
         * Define custom_models
         */
        'custom_models' => [
            //
        ],

        /*
         * Define excluded_models
         */
        'excluded_models' => [
            //
        ],

        'excluded_policy_models' => [
            User::class,
        ],

        /*
         * Define any other permission that should be synced with the DB
         */
        'custom_permissions' => [
            //'view-log'
        ],

        'user_model' => User::class,

        'policies_namespace' => 'App\Policies',
    ],
];
