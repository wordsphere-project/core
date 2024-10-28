<?php

use Illuminate\Database\Eloquent\Collection;
use WordSphere\Core\Database\Seeders\TestTypeSeeder;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;
use WordSphere\Core\Infrastructure\Types\Persistence\Models\TypeRelationshipModel;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;

describe('Content Model', function () {

    beforeEach(function () {
        $this->artisan('db:seed', ['--class' => TestTypeSeeder::class]);
        $this->tenantProvider = app(TenantProjectProvider::class);
    });

    test('can persist content with type', function () {

        $content = ContentModel::query()->create(
            attributes: [
                'type' => 'pages',
                'title' => 'Test Article',
                'slug' => 'test-article',
                'content' => 'Test content',
                'tenant_id' => $this->tenantProvider->getCurrentTenantId()->toString(),
                'project_id' => $this->tenantProvider->getCurrentProjectId()->toString(),
            ]
        );

        expect($content)->toBeInstanceOf(ContentModel::class)
            ->and($content->type)->toBe('pages')
            ->and($content->title)->toBe('Test Article');

        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
            'type' => 'pages',
            'title' => 'Test Article',
        ]);
    });

    test('can manage type relationships', function () {
        $page = ContentModel::query()->create(
            attributes: [
                'type' => 'pages',
                'title' => 'Test Page',
                'slug' => 'test-page',
                'content' => 'Test content',
                'tenant_id' => $this->tenantProvider->getCurrentTenantId()->toString(),
                'project_id' => $this->tenantProvider->getCurrentProjectId()->toString(),
            ]
        );

        $block = ContentModel::query()->create(
            attributes: [
                'type' => 'blocks',
                'title' => 'Test Block',
                'slug' => 'test-block',
                'tenant_id' => $this->tenantProvider->getCurrentTenantId()->toString(),
                'project_id' => $this->tenantProvider->getCurrentProjectId()->toString(),
            ]
        );

        $relation = TypeRelationshipModel::query()->create(
            attributes: [
                'id' => $relationId = Uuid::generate(),
                'source_id' => $page->id,
                'source_type' => 'pages',
                'target_id' => $block->id,
                'target_type' => 'blocks',
                'relation_name' => 'blocks',
                'tenant_id' => $this->tenantProvider->getCurrentTenantId()->toString(),
                'project_id' => $this->tenantProvider->getCurrentProjectId()->toString(),
            ]
        );

        //Assert
        /** @phpstan-ignore-next-line  */
        expect($relation)
            ->source_id->toBe($page->id)
            ->target_id->toBe($block->id)
            ->relation_name->toBe('blocks');

    });

    test('can query related content through relationships', function () {

        //Create Contents
        $page = ContentModel::query()->create(
            attributes: [
                'type' => 'pages',
                'title' => 'Test Page',
                'slug' => 'test-page',
                'content' => 'Test content',
                'tenant_id' => $this->tenantProvider->getCurrentTenantId()->toString(),
                'project_id' => $this->tenantProvider->getCurrentProjectId()->toString(),
            ]
        );

        $block = ContentModel::query()->create(
            attributes: [
                'type' => 'blocks',
                'title' => 'Test Block',
                'slug' => 'test-block',
                'tenant_id' => $this->tenantProvider->getCurrentTenantId()->toString(),
                'project_id' => $this->tenantProvider->getCurrentProjectId()->toString(),
            ]
        );

        //Create Relation
        $relation = TypeRelationshipModel::query()->create(
            attributes: [
                'id' => $relationId = Uuid::generate(),
                'source_id' => $page->id,
                'source_type' => 'pages',
                'target_id' => $block->id,
                'target_type' => 'blocks',
                'relation_name' => 'blocks',
                'tenant_id' => $this->tenantProvider->getCurrentTenantId()->toString(),
                'project_id' => $this->tenantProvider->getCurrentProjectId()->toString(),
            ]
        );

        //Act
        $page = $page->fresh();
        /** @phpstan-ignore-next-line  */
        $relatedBlocks = $page->blocks()->get();

        expect($relatedBlocks)
            ->toBeInstanceOf(Collection::class)
            ->toHaveCount(1)
            ->and($relatedBlocks->first())
            ->toBeInstanceOf(ContentModel::class)
            ->and($relatedBlocks->first()->id)->toBe($block->id);

    });

});
