<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\Factories\ContentManagement;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Enums\ContentStatus;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ContentType;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel as EloquentArticle;

/**
 * @template TModel of EloquentArticle
 *
 * @extends Factory<TModel>
 */
final class ContentFactory extends Factory
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

        $contentType = new ContentType(
            key: 'blog-posts',
            singularName: 'Post',
            pluralName: 'Posts',
            navigationGroup: '',
            description: '',
            icon: ''
        );

        return [
            'id' => Uuid::generate(),
            'type' => $contentType,
            'title' => $title,
            'slug' => Slug::fromString($title),
            'content' => $this->faker->paragraphs(5, true),
            'excerpt' => $this->faker->paragraph,
            'status' => $this->faker->randomElement([ContentStatus::DRAFT, ContentStatus::PUBLISHED]),
            'createdAt' => DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 year')),
            'updatedAt' => DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 month')),
            'createdBy' => Uuid::generate(),
            'updatedBy' => Uuid::generate(),
            'publishedAt' => null,
            'customFields' => [],
        ];
    }

    public function published(): ContentFactory
    {
        return $this->state(function (array $attributes): array {
            return [
                'status' => ContentStatus::PUBLISHED,
                'publishedAt' => DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 month')),
            ];
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function makeForDomain($attributes = [], null|Model|Content $parent = null): Model|Content|EloquentArticle|null
    {
        $articleData = array_merge($this->definition(), $attributes);

        return new Content(
            id: $articleData['id'],
            type: $articleData['type'],
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

    public function create($attributes = [], ?Model $parent = null): Model|Collection|EloquentArticle|Content
    {
        $article = $this->makeForDomain($attributes, $parent);
        app(abstract: ContentRepositoryInterface::class)
            ->save($article);

        return $article;
    }
}
