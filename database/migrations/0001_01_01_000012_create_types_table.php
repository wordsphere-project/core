<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('types', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->string('entity_class');
            $table->uuid('parent_id')->nullable();
            $table->uuid('tenant_id');
            $table->uuid('project_id');
            $table->jsonb('interface_data')->nullable();
            $table->jsonb('custom_fields')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['key', 'tenant_id', 'project_id']);
            $table->index(['tenant_id', 'project_id']);

        });

        Schema::create('allowed_relations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('source_type_id');
            $table->uuid('target_type_id');
            $table->string('name');
            $table->string('relation_type');
            $table->boolean('is_required')->default(false);
            $table->integer('min_items')->nullable();
            $table->integer('max_items')->nullable();
            $table->string('inverse_relation_name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->uuid('tenant_id');
            $table->uuid('project_id');
            $table->timestamps();
            $table->softDeletes();

            // Relationships
            $table->foreign('source_type_id')
                ->references('id')
                ->on('types')
                ->onDelete('cascade');

            $table->foreign('target_type_id')
                ->references('id')
                ->on('types')
                ->onDelete('cascade');

            // Ensure unique relations per tenant/project
            $table->unique([
                'source_type_id',
                'name',
                'tenant_id',
                'project_id',
            ], 'unique_type_relations');

            // Indexes
            $table->index(['tenant_id', 'project_id']);
            $table->index(['source_type_id', 'target_type_id']);
        });

        Schema::create('type_relationships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('source_id');
            $table->string('source_type');
            $table->uuid('target_id');
            $table->string('target_type');
            $table->string('relation_name');
            $table->integer('sort_order')->nullable();
            $table->json('meta_data')->nullable();
            $table->uuid('tenant_id');
            $table->uuid('project_id');
            $table->timestamps();
            $table->softDeletes();

            // Ensure unique relationships where needed
            $table->unique([
                'source_id',
                'target_id',
                'relation_name',
                'tenant_id',
                'project_id',
            ], 'unique_content_relationships');

            // Indexes
            $table->index(['tenant_id', 'project_id']);
            $table->index(['source_id', 'source_type']);
            $table->index(['target_id', 'target_type']);
            $table->index('relation_name');
        });

        Schema::create('type_field_validations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('type_id');
            $table->string('field_key');
            $table->string('validation_rule');
            $table->json('validation_params')->nullable();
            $table->uuid('tenant_id');
            $table->uuid('project_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('type_id')
                ->references('id')
                ->on('types')
                ->onDelete('cascade');

            // Indexes
            $table->index(['tenant_id', 'project_id']);
            $table->index(['type_id', 'field_key']);
        });

        // Field dependencies for types
        Schema::create('type_field_dependencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('type_id');
            $table->string('field_key');
            $table->string('depends_on_field');
            $table->string('dependency_type');
            $table->json('dependency_config')->nullable();
            $table->uuid('tenant_id');
            $table->uuid('project_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('type_id')
                ->references('id')
                ->on('types')
                ->onDelete('cascade');

            // Indexes
            $table->index(['tenant_id', 'project_id']);
            $table->index(['type_id', 'field_key']);
            $table->index('depends_on_field');
        });

        Artisan::call('types:register');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_field_dependencies');
        Schema::dropIfExists('type_field_validations');
        Schema::dropIfExists('type_relationships');
        Schema::dropIfExists('type_allowed_relations');
        Schema::dropIfExists('types');
    }
};
