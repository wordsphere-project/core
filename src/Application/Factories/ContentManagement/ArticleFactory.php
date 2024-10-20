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
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleUuid;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article as EloquentArticle;

/**
 * @template TModel of EloquentArticle
 *
 * @extends Factory<TModel>
 */
final class ArticleFactory extends Factory
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
            'id' => ArticleUuid::generate(),
            'title' => $title,
            'slug' => Slug::fromString($title),
            'content' => $this->faker->paragraphs(5, true),
            'excerpt' => $this->faker->paragraph,
            'status' => $this->faker->randomElement([ArticleStatus::DRAFT, ArticleStatus::PUBLISHED]),
            'createdAt' => DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 year')),
            'updatedAt' => DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 month')),
            'createdBy' => Uuid::generate(),
            'updatedBy' => Uuid::generate(),
            'publishedAt' => null,
            'customFields' => [],
        ];
    }

    public function published(): ArticleFactory
    {
        return $this->state(function (array $attributes): array {
            return [
                'status' => ArticleStatus::PUBLISHED,
                'publishedAt' => DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 month')),
            ];
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function makeForDomain($attributes = [], null|Model|DomainArticle $parent = null): Model|DomainArticle|EloquentArticle|null
    {
        $articleData = array_merge($this->definition(), $attributes);

        return new DomainArticle(
            id: $articleData['id'],
            title: $articleData['title'],
            slug: $articleData['slug'],
            createdBy: $articleData['createdBy'],
            updatedBy: $articleData['updatedBy'],
            content: $articleData['content'],
            excerpt: $articleData['excerpt'],
            customFields: $articleData['customFields'],
            status: $articleData['status'],
            publishedAt: $articleData['publishedAt'],
            createdAt: $articleData['createdAt'],
            updatedAt: $articleData['updatedAt'],
        );
    }

    public function create($attributes = [], ?Model $parent = null): Model|Collection|EloquentArticle|DomainArticle
    {
        $article = $this->makeForDomain($attributes, $parent);
        app(abstract: ArticleRepositoryInterface::class)
            ->save($article);

        return $article;
    }
}
