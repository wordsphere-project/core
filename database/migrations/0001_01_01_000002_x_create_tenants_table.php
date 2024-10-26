<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table): void {
            $table->uuid('id')->primary();

            $table->foreignUuid('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onDelete('cascade');

            $table->string('name');
            $table->string('type')->default('website');
            $table->jsonb('domains')->nullable();
            $table->softDeletes();
            $table->timestamps();

        });

        DB::table('tenants')->insert([
            'id' => $tenantId = Ramsey\Uuid\Uuid::uuid4()->toString(),
            'name' => 'WordSphere',
        ]);

        DB::table('projects')->insert([
            'id' => Ramsey\Uuid\Uuid::uuid4()->toString(),
            'name' => 'WordSphere',
            'type' => 'website',
            'domains' => json_encode([
                'wordsphere.test',
                'localhost',
            ]),
            'tenant_id' => $tenantId,
        ]);

    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
