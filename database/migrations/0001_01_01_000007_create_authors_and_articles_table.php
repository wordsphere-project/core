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
            $table->unsignedInteger('featured_image_id')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by');
            $table->timestamps();

            $table->foreign('featured_image_id')
                ->references('id')
                ->on(app(config('wordsphere.curator.model'))
                    ->getTable())
                ->onDelete('set null');

            $table->foreign('created_by')
                ->references('uuid')
                ->on('users');

            $table->foreign('updated_by')
                ->references('uuid')
                ->on('users');
        });

        Schema::create('articles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->json('custom_fields')->nullable();
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
                ->on(app(config('wordsphere.curator.model'))
                    ->getTable());

            $table->foreign('created_by')
                ->references('uuid')
                ->on('users');

            $table->foreign('updated_by')
                ->references('uuid')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
        Schema::dropIfExists('authors');
    }
};
