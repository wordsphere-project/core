<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentAuthor;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        EloquentAuthor::factory()
            ->count(10)
            ->withBio()
            ->withSocialLinks('social_links')
            ->withWebsite()
            ->create();

    }
}
