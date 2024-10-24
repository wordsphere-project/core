<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use WordSphere\Core\Interfaces\Http\Controllers\Api\PageController;

Route::prefix('api/v1')->group(function (): void {

    Route::group(['prefix' => 'pages'], function (): void {
        Route::get('by-path/{path}', [PageController::class, 'getByPath'])
            ->where('path', '.*')
            ->where('format', 'json|xml')
            ->name('api.v1.page.by.path');

        Route::get('by-slug/{slug}', [PageController::class, 'getBySlug'])
            ->where('slug', '.*')
            ->where('format', 'json|xml')
            ->name('api.v1.page.by.slug');

        // Other page-related endpoints
    });
});