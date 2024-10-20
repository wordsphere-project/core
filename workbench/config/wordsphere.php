<?php

use WordSphere\Core\Legacy\Support\CustomFields\CustomFieldsManager;
use Workbench\App\Models\User;

return [
    'user_model' => User::class,
    'custom_fields' => [
        'manager' => CustomFieldsManager::class,
    ],
];
