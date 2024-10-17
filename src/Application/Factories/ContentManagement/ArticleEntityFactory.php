<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\Factories\ContentManagement;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Domain\ContentManagement\Enums\ArticleStatus;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article;

/**
 * @template TModel of Article
 *
 * @extends Factory<TModel>
 */
final class ArticleEntityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Article::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {

        $title = $this->faker->sentence;

        return [
            'id' => ArticleId::generate(),
            'title' => $title,
            'slug' => Slug::fromString($title),
            'content' => $this->faker->paragraphs(5, true),
            'excerpt' => $this->faker->paragraph,
            'status' => $this->faker->randomElement([ArticleStatus::DRAFT, ArticleStatus::PUBLISHED]),
            'createdAt' => \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 year')),
            'updatedAt' => \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 month')),
            'publishedAt' => null,
            'data' => [],
        ];
    }

    public function published(): ArticleEntityFactory
    {
        return $this->state(function (array $attributes): array {
            return [
                'status' => ArticleStatus::PUBLISHED,
                'publishedAt' => \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 month')),
            ];
        });
    }

    public function make($attributes = [], ?Model $parent = null)
    {
        $articleData = array_merge($this->definition(), $attributes);

        return new Article(
            id: $articleData['id'],
            title: $articleData['title'],
            slug: $articleData['slug'],
            content: $articleData['content'],
            excerpt: $articleData['excerpt'],
            data: $articleData['data'],
            status: $articleData['status'],
            createdAt: $articleData['createdAt'],
            updatedAt: $articleData['updatedAt'],
            publishedAt: $articleData['publishedAt'],
        );
    }

    public function create($attributes = [], ?Model $parent = null)
    {
        $article = $this->make($attributes, $parent);
        app(abstract: ArticleRepositoryInterface::class)
            ->save($article);

        return $article;
    }
}
