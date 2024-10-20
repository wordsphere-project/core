<?php

namespace WordSphere\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentPage;
use WordSphere\Core\Legacy\Enums\ContentStatus;
use WordSphere\Core\Legacy\Enums\ContentVisibility;

use function now;

/**
 * @template TModel of EloquentPage
 *
 * @extends Factory<TModel>
 */
class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = EloquentPage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $title = $this->faker->unique()->words(4, true),
            'path' => '/'.Str::slug($title),
            'content' => $this->faker->paragraphs(4, true),
            'excerpt' => $this->faker->paragraph(),
            'template' => 'default',
            'sort_order' => 1,
            'status' => $this->faker->randomElement(ContentStatus::cases()),
            'visibility' => $this->faker->randomElement(ContentVisibility::cases()),
            'publish_at' => now(),
        ];
    }
}
