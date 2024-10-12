<?php

declare(strict_types=1);

// config for VendorName/Skeleton
return [

    'themes' => [
        'path' => base_path('themes'),
    ],
    'paths_to_scan_for_routeable_models' => [
        app_path(),
        wordsphere_path(),
    ],

    'auth' => [
        'providers' => [
            'user' => [
                'model' => \WordSphere\Core\Models\User::class
            ]
        ]
    ],

    'permission' => [
        'models' => [

            /*
             * When using the "HasPermissions" trait from this package, we need to know which
             * Eloquent model should be used to retrieve your permissions. Of course, it
             * is often just the "Permission" model but you may use whatever you like.
             *
             * The model you want to use as a Permission model needs to implement the
             * `Spatie\Permission\Contracts\Permission` contract.
             */

            'permission' => Spatie\Permission\Models\Permission::class,

            /*
             * When using the "HasRoles" trait from this package, we need to know which
             * Eloquent model should be used to retrieve your roles. Of course, it
             * is often just the "Role" model but you may use whatever you like.
             *
             * The model you want to use as a Role model needs to implement the
             * `Spatie\Permission\Contracts\Role` contract.
             */

            'role' => Spatie\Permission\Models\Role::class,

        ],
        'table_names' => [
            'roles' => 'roles',
            'permissions' => 'permissions',
            'model_has_permissions' => 'model_has_permissions',
            'model_has_roles' => 'model_has_roles',
            'role_has_permissions' => 'role_has_permissions',
        ],
        'column_names' => [
            /*
             * Change this if you want to name the related pivots other than defaults
             */
            'role_pivot_key' => null, //default 'role_id',
            'permission_pivot_key' => null, //default 'permission_id',

            /*
             * Change this if you want to name the related model primary key other than
             * `model_id`.
             *
             * For example, this would be nice if your primary keys are all UUIDs. In
             * that case, name this `model_uuid`.
             */

            'model_morph_key' => 'model_id',

            /*
             * Change this if you want to use the teams feature and your related model's
             * foreign key is other than `team_id`.
             */

            'team_foreign_key' => 'team_id',
        ],
        'teams' => false,
        'use_passport_client_credentials' => false,
        'display_permission_in_exception' => false,
        'display_role_in_exception' => false,
        'enable_wildcard_permission' => false,
        'cache' => [

            /*
             * By default all permissions are cached for 24 hours to speed up performance.
             * When permissions or roles are updated the cache is flushed automatically.
             */

            'expiration_time' => DateInterval::createFromDateString('24 hours'),

            /*
             * The cache key used to store all permissions.
             */

            'key' => 'spatie.permission.cache',

            /*
             * You may optionally indicate a specific cache driver to use for permission and
             * role caching using any of the `store` drivers listed in the cache.php config
             * file. Using 'default' here means to use the `default` set in cache.php.
             */

            'store' => 'default',
        ],
    ],
    'curator' => [
        'accepted_file_types' => [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/svg+xml',
            'application/pdf',
        ],
        'cloud_disks' => [
            's3',
            'cloudinary',
            'imgix',
        ],
        'curation_formats' => [
            'jpg',
            'jpeg',
            'webp',
            'png',
            'avif',
        ],
        'curation_presets' => [
            Awcodes\Curator\Curations\ThumbnailPreset::class,
        ],
        'directory' => 'media',
        'disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),
        'glide' => [
            'server' => Awcodes\Curator\Glide\DefaultServerFactory::class,
            'fallbacks' => [],
            'route_path' => 'curator',
        ],
        'image_crop_aspect_ratio' => null,
        'image_resize_mode' => null,
        'image_resize_target_height' => null,
        'image_resize_target_width' => null,
        'is_limited_to_directory' => false,
        'is_tenant_aware' => true,
        'tenant_ownership_relationship_name' => 'tenant',
        'max_size' => 5000,
        'model' => Awcodes\Curator\Models\Media::class,
        'min_size' => 0,
        'path_generator' => null,
        'resources' => [
            'label' => 'Media',
            'plural_label' => 'Media',
            'navigation_group' => null,
            'cluster' => null,
            'navigation_label' => 'Media',
            'navigation_icon' => 'heroicon-o-photo',
            'navigation_sort' => null,
            'navigation_count_badge' => false,
            'resource' => Awcodes\Curator\Resources\MediaResource::class,
        ],
        'should_preserve_filenames' => false,
        'should_register_navigation' => true,
        'should_check_exists' => true,
        'visibility' => 'public',
        'tabs' => [
            'display_curation' => true,
            'display_upload_new' => true,
        ],
        'multi_select_key' => 'metaKey',
        'table' => [
            'layout' => 'grid',
        ],
    ],

];
