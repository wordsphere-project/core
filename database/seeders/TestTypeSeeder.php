<?php

declare(strict_types=1);

namespace WordSphere\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use WordSphere\Core\Application\ContentManagement\ContentTypes\BlockContentTypeRegistrar;
use WordSphere\Core\Application\ContentManagement\ContentTypes\PageContentTypeRegistrar;
use WordSphere\Core\Infrastructure\Tenancy\Persistence\Models\EloquentProject;
use WordSphere\Core\Infrastructure\Tenancy\Persistence\Models\EloquentTenant;

class TestTypeSeeder extends Seeder
{
    public function run(): void
    {

        $tenant = EloquentTenant::query()->create([
            'name' => 'Test Tenant',
        ]);
        EloquentProject::query()->create([
            'name' => 'Test Project',
            'tenant_id' => $tenant->id,
        ]);

        $blockTypeRegistrar = app(BlockContentTypeRegistrar::class);
        $pageTypeRegistrar = app(PageContentTypeRegistrar::class);

        $blockTypeRegistrar->register();
        $pageTypeRegistrar->register();

        $blockTypeRegistrar->processPendingRelations();
        $pageTypeRegistrar->processPendingRelations();

    }
}
