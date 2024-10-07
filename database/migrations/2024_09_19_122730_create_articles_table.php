<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use WordSphere\Core\Enums\ContentStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')
                ->index()
                ->unique();
            $table->text('content')
                ->nullable();
            $table->foreignIdFor(Media::class)
                ->nullable()
                ->constrained('media')
                ->nullOnDelete();
            $table->integer('status')
                ->default(ContentStatus::DRAFT);
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
