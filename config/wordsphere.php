<?php

declare(strict_types=1);

// config for VendorName/Skeleton
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Legacy\Support\CustomFields\CustomFieldsManager;

return [

    'themes' => [
        'path' => base_path('themes'),
    ],
    'paths_to_scan_for_routeable_models' => [
        app_path(),
        wordsphere_path(),
    ],
    'custom_fields' => [
        'manager' => CustomFieldsManager::class,
    ],

    'auth' => [
        'providers' => [
            'user' => [
                'model' => EloquentUser::class,
            ],
        ],
    ],
];
