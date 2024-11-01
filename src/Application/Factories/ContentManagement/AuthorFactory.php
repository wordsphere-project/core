<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\Factories\ContentManagement;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Domain\ContentManagement\Entities\Author as DomainAuthor;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentAuthor;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentMedia;
use WordSphere\Core\Infrastructure\Identity\Persistence\UserModel;

use function array_key_exists;

/**
 * @template TModel of EloquentAuthor
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
    protected $model = EloquentAuthor::class;

    public function definition(): array
    {
        /** @var UserModel $creator */
        $creator = UserModel::factory()
            ->create();

        return [
            'id' => Uuid::generate(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'photo' => null,
            'created_by' => Uuid::fromString($creator->uuid),
            'updated_by' => Uuid::fromString($creator->uuid),
        ];
    }

    public function configure(): AuthorFactory
    {
        return $this->afterMaking(function (EloquentAuthor $author): void {
            /** @var EloquentMedia $media */
            $media = MediaFactory::new()->create();
            $author->photo = $media->path;
            $author->save();
        });
    }

    public function withoutFeaturedImage(): AuthorFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'photo' => null,
            ];
        });
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

    public function withSocialLinks(string $key = 'socialLinks'): AuthorFactory
    {

        return $this->state(function (array $attributes) use ($key): array {
            return [
                $key => [
                    'facebook' => $this->faker->userName,
                    'twitter' => $this->faker->userName,
                    'instagram' => $this->faker->userName,
                    'youtube' => $this->faker->userName,
                    'github' => $this->faker->userName,
                    'pinkary' => $this->faker->userName,
                    'linkedin' => $this->faker->userName,
                ],
            ];
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function makeForDomain($attributes = [], null|Model|DomainAuthor $parent = null): Model|DomainAuthor
    {
        $authorData = array_merge($this->definition(), $attributes);

        return new DomainAuthor(
            id: $authorData['id'],
            name: $authorData['name'],
            createdBy: $authorData['created_by'],
            updatedBy: $authorData['updated_by'],
            email: $authorData['email'],
            bio: array_key_exists('bio', $authorData) ? $authorData['bio'] : null,
            website: array_key_exists('bio', $authorData) ? $authorData['website'] : null,
            socialLinks: array_key_exists('bio', $authorData) ? $authorData['socialLinks'] : [],
        );
    }
}
