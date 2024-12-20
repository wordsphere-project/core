<?php

declare(strict_types=1);

namespace WordSphere\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;

/**
 * @extends Factory<ContentModel>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
