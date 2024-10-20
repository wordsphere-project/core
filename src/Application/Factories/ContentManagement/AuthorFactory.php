<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\Factories\ContentManagement;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Domain\ContentManagement\Entities\Author as DomainAuthor;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\AuthorId;
use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article as EloquentArticle;

use function array_key_exists;

/**
 * @template TModel of EloquentArticle
 *
 * @extends Factory<TModel>
 */
final class AuthorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = EloquentArticle::class;

    public function definition(): array
    {
        return [
            'id' => AuthorId::generate(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'creator' => UserUuid::generate(),
        ];
    }

    public function withBio(): AuthorFactory
    {
        return $this->state(function (array $attributes): array {
            return [
                'bio' => $this->faker->paragraphs(3, true),
            ];
        });
    }

    public function withWebsite(): AuthorFactory
    {
        return $this->state(function (array $attributes): array {
            return [
                'website' => $this->faker->url(),
            ];
        });
    }

    public function withSocialLinks(): AuthorFactory
    {
        return $this->state(function (array $attributes): array {
            return [
                'socialLinks' => [
                    'facebook' => $this->faker->url(),
                    'twitter' => $this->faker->url(),
                    'instagram' => $this->faker->url(),
                    'youtube' => $this->faker->url(),
                    'github' => $this->faker->url(),
                    'pinkary' => $this->faker->url(),
                ],
            ];
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function make($attributes = [], null|Model|DomainAuthor $parent = null): Model|DomainAuthor
    {
        $articleData = array_merge($this->definition(), $attributes);

        return new DomainAuthor(
            id: $articleData['id'],
            name: $articleData['name'],
            email: $articleData['email'],
            creator: $articleData['creator'],
            bio: array_key_exists('bio', $articleData) ? $articleData['bio'] : null,
            website: array_key_exists('bio', $articleData) ? $articleData['website'] : null,
            socialLinks: array_key_exists('bio', $articleData) ? $articleData['socialLinks'] : [],
        );
    }

    /**
    public function create($attributes = [], ?Model $parent = null): Model|Collection|EloquentArticle|DomainAuthor
    {
        $article = $this->make($attributes, $parent);
        app(abstract: ArticleRepositoryInterface::class)
            ->save($article);

        return $article;
    }*/
}
