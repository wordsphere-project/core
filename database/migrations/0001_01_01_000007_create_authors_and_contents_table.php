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

        Schema::create('authors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->text('bio')->nullable();
            $table->string('website')->nullable();
            $table->json('social_links')->nullable();
            $table->string('photo')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by');
            $table->timestamps();

            $table->foreign('created_by')
                ->references('uuid')
                ->on('users');

            $table->foreign('updated_by')
                ->references('uuid')
                ->on('users');
        });

        Schema::create('contents', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type')->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->jsonb('custom_fields')->nullable();
            $table->string('status');
            $table->uuid('author_id')->nullable();
            $table->unsignedInteger('featured_image_id')->nullable();
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by');

            $table->foreign('author_id')
                ->references('id')
                ->on('authors')
                ->onDelete('set null');

            $table->foreign('featured_image_id')
                ->references('id')
                ->on(app(config('curator.model'))
                    ->getTable());

            $table->foreign('created_by')
                ->references('uuid')
                ->on('users');

            $table->foreign('updated_by')
                ->references('uuid')
                ->on('users');
        });

        Schema::create('content_media', function (Blueprint $table): void {
            $table->id();
            $table->uuid('content_id');
            $table->unsignedInteger('media_id');
            $table->integer('order')->default(0);
            $table->jsonb('attributes')->nullable();
            $table->timestamps();

            $table->foreign('content_id')
                ->references('id')
                ->on('contents')
                ->onDelete('cascade');

            $table->foreign('media_id')
                ->references('id')
                ->on('media')
                ->onDelete('cascade');

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_media');
        Schema::dropIfExists('contents');
        Schema::dropIfExists('authors');
    }
};