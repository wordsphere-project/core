<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use WordSphere\Core\Enums\SystemRole;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (SystemRole::cases() as $role) {
            Role::findOrCreate(name: $role->value, guardName: 'web');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
