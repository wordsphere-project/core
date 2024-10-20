<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Legacy\Enums\SystemRole;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        /* @var EloquentUser $francisco */
        $francisco = EloquentUser::query()->createOrFirst([
            'email' => 'francisco.barrento@bdynamic.pt',
        ], [
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
            'name' => 'Francisco Barrento',
        ]);

        /* @var EloquentUser $joao */
        $joao = EloquentUser::query()->createOrFirst([
            'email' => 'joaopanoias@gmail.com',
        ], [
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
            'name' => 'João Panoias',
        ]);

        $superAdmin = Role::findByName(
            name: SystemRole::SUPER_ADMIN->value
        );

        $francisco->assignRole([
            $superAdmin,
        ]);
        $joao->assignRole([
            $superAdmin,
        ]);

    }
}
