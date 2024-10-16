<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use WordSphere\Core\Legacy\Livewire\Pages\ManageTheme;

Route::get('admin/manage-theme/{theme}', ManageTheme::class);
