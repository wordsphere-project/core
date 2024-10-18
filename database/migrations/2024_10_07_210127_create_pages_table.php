<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
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
            $table->id();
            $table->string('title');
            $table->string('path')
                ->unique();
            $table->longText('content')
                ->nullable();
            $table->text('excerpt')
                ->nullable();
            $table->string('template');
            $table->string('redirect_url')
                ->nullable();
            $table->integer('sort_order');
            $table->integer('status')
                ->default(ContentStatus::DRAFT->value);
            $table->integer('visibility')
                ->default(ContentVisibility::PUBLIC->value);

            $table->foreignIdFor(config('wordsphere.auth.providers.user.model'))
                ->nullable()
                ->constrained(app(config('wordsphere.auth.providers.user.model'))->getTable())
                ->nullOnDelete();

            $table->foreignIdFor(Media::class)
                ->nullable()
                ->constrained(app(config('curator.model'))->getTable())
                ->nullOnDelete();

            $table->dateTime('publish_at');
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
