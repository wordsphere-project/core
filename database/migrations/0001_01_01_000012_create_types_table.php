<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->timestamps();

            $table->unique(['key', 'tenant_id', 'project_id']);
            $table->index(['tenant_id', 'project_id']);

        });

        Schema::create('allowed_relations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('source_type_id');
            $table->uuid('target_type_id');
            $table->string('relation_type');
            $table->boolean('is_required')->default(false);
            $table->integer('min_items')->nullable();
            $table->integer('max_items')->nullable();
            $table->string('inverse_relation_name')->nullable();
            $table->uuid('tenant_id');
            $table->uuid('project_id');
            $table->timestamps();

            $table->foreign('source_type_id')->references('id')->on('types')->onDelete('cascade');
            $table->foreign('target_type_id')->references('id')->on('types')->onDelete('cascade');
            $table->unique(['source_type_id', 'name', 'tenant_id', 'project_id']);
            $table->index(['tenant_id', 'project_id']);
        });

        Schema::create('relationships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('source');
            $table->uuidMorphs('target');
            $table->string('relation_name');
            $table->integer('sort_order')->nullable();
            $table->uuid('tenant_id');
            $table->uuid('project_id');
            $table->timestamps();

            $table->index(['tenant_id', 'project_id']);
            $table->index(['source_id', 'source_type', 'relation_name']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relationships');
        Schema::dropIfExists('allowed_relations');
        Schema::dropIfExists('types');
    }
};
