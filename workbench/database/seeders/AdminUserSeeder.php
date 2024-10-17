<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use WordSphere\Core\Legacy\Enums\SystemRole;
use WordSphere\Core\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        /* @var User $francisco */
        $francisco = User::query()->createOrFirst([
            'email' => 'francisco.barrento@bdynamic.pt',
        ], [
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
            'name' => 'Francisco Barrento',
        ]);

        /* @var User $joao */
        $joao = User::query()->createOrFirst([
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
