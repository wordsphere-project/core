<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use WordSphere\Core\Infrastructure\Identity\Persistence\UserModel;
use WordSphere\Core\Legacy\Enums\SystemRole;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        /* @var UserModel $francisco */
        $test1 = UserModel::query()->createOrFirst([
            'email' => 'test@wordsphere.io',
        ], [
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'name' => 'Bruce Wayne',
        ]);

        /* @var UserModel $joao */
        $test2 = UserModel::query()->createOrFirst([
            'email' => 'test+2@wordsphere.io',
        ], [
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
            'name' => 'John Doe',
        ]);

        $superAdmin = Role::findByName(
            name: SystemRole::SUPER_ADMIN->value
        );

        $test1->assignRole([
            $superAdmin,
        ]);
        $test2->assignRole([
            $superAdmin,
        ]);

    }
}
