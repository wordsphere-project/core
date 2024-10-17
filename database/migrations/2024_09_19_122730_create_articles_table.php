<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
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
        Schema::create('articles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')
                ->index()
                ->unique();
            $table->text('excerpt')
                ->nullable();
            $table->text('content')
                ->nullable();
            $table->jsonb('data')
                ->nullable();
            $table->string('status')
                ->index();
            $table->foreignIdFor(Media::class)
                ->nullable()
                ->constrained('media')
                ->nullOnDelete();
            $table->timestamp('published_at')
                ->nullable();
            $table->timestamp('publish_at')
                ->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
