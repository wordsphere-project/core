<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\Factories\ContentManagement;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Domain\ContentManagement\Entities\Article as DomainArticle;
use WordSphere\Core\Domain\ContentManagement\Enums\ArticleStatus;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article as EloquentArticle;

/**
 * @template TModel of EloquentArticle
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
    protected $model = EloquentArticle::class;


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
            'createdAt' => DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 year')),
            'updatedAt' => DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 month')),
            'publishedAt' => null,
            'data' => [],
        ];
    }

    public function published(): ArticleEntityFactory
    {
        return $this->state(function (array $attributes): array {
            return [
                'status' => ArticleStatus::PUBLISHED,
                'publishedAt' => DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 month')),
            ];
        });
    }

    /**
     * @param array<string, mixed> $attributes
     * @param Model|DomainArticle|null $parent
     * @return Model|DomainArticle|EloquentArticle
     */
    public function make($attributes = [], null|Model|DomainArticle $parent = null): Model|DomainArticle|EloquentArticle
    {
        $articleData = array_merge($this->definition(), $attributes);

        return new DomainArticle(
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

    public function create($attributes = [], ?Model $parent = null): Model|Collection|EloquentArticle|DomainArticle
    {
        $article = $this->make($attributes, $parent);
        app(abstract: ArticleRepositoryInterface::class)
            ->save($article);

        return $article;
    }
}
