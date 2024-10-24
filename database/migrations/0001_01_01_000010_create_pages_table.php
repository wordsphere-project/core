<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use WordSphere\Core\Legacy\Enums\ContentStatus;
use WordSphere\Core\Legacy\Enums\ContentVisibility;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('path')
                ->unique();
            $table->string('slug')
                ->unique();
            $table->longText('content')
                ->nullable();
            $table->text('excerpt')
                ->nullable();
            $table->string('template')->nullable();
            $table->string('redirect_url')
                ->nullable();
            $table->integer('sort_order');
            $table->integer('status')
                ->default(ContentStatus::DRAFT->value);
            $table->integer('visibility')
                ->default(ContentVisibility::PUBLIC->value);
            $table->unsignedInteger('featured_image_id')->nullable();
            $table->jsonb('custom_fields')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by');

            $table->foreignIdFor(config('wordsphere.auth.providers.user.model'))
                ->nullable()
                ->constrained(app(config('wordsphere.auth.providers.user.model'))->getTable())
                ->nullOnDelete();

            $table->foreign('featured_image_id')
                ->references('id')
                ->on(app(config('wordsphere.curator.model'))
                    ->getTable());

            $table->foreign('created_by')
                ->references('uuid')
                ->on('users');

            $table->foreign('updated_by')
                ->references('uuid')
                ->on('users');

            $table->dateTime('publish_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
