<?php

use WordSphere\Core\Support\CustomFields\CustomFieldsManager;

return [
    'user_model' => \Workbench\App\Models\User::class,
    'custom_fields' => [
        'manager' => CustomFieldsManager::class,
    ],
];
