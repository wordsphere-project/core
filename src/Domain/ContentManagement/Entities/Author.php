<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Entities;

use InvalidArgumentException;
use WordSphere\Core\Domain\ContentManagement\DTOs\AuthorStateDTO;
use WordSphere\Core\Domain\ContentManagement\Events\AuthorUpdated;
use WordSphere\Core\Domain\Shared\Concerns\HasAuditTrail;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

use function in_array;

class Author
{
    use HasAuditTrail;

    private Uuid $id;

    private string $name;

    private ?string $email;

    private ?string $bio;

    private ?string $website;

    private ?string $photo;

    private array $socialLinks;

    private array $domainEvents = [];

    private array $changedFields = [];

    private const ALLOWED_SOCIAL_PLATFORMS = [
        'twitter',
        'linkedin',
        'facebook',
        'instagram',
        'github',
        'pinkary',
        'youtube',
    ];

    public function __construct(
        Uuid $id,
        string $name,
        Uuid $createdBy,
        Uuid $updatedBy,
        ?string $email = null,
        ?string $bio = null,
        ?string $website = null,
        ?string $photo = null,
        ?array $socialLinks = [],
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->bio = $bio;
        $this->website = $website;
        $this->photo = $photo;
        $this->socialLinks = $socialLinks;

        $this->createdBy = $createdBy;
        $this->updatedBy = $updatedBy;

        $this->initializeTimestamps();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function getSocialLinks(): array
    {
        return $this->socialLinks;
    }

    public function updateName(string $name): void
    {

        if (empty(trim($name))) {
            throw new InvalidArgumentException('Author name cannot be empty.');
        }

        $this->name = $name;
        $this->changedFields['name'] = $name;
        $this->updateAuditTrail();
    }

    public function updateEmail(string $email): void
    {

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        $this->email = $email;
        $this->changedFields['email'] = $email;
        $this->updateAuditTrail();
    }

    public function updateBio(?string $bio): void
    {
        $this->bio = $bio;
        $this->changedFields['bio'] = $bio;
        $this->updateAuditTrail();
    }

    public function updateWebsite(?string $website): void
    {
        if (! $website !== null && ! filter_var($website, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid website URL.');
        }
        $this->website = $website;
        $this->changedFields['website'] = $website;
        $this->updateAuditTrail();
    }

    public function updatePhoto(?string $photo): void
    {
        $this->photo = $photo;
        $this->changedFields['photo'] = $photo;
        $this->updateAuditTrail();
    }

    public function updateSocialLinks(array $socialLinks): void
    {
        foreach ($socialLinks as $platform => $username) {
            $this->validateSocialPlatform($platform);
        }

        $this->socialLinks = $socialLinks;
        $this->changedFields['socialLinks'] = $socialLinks;
        $this->updateAuditTrail();
    }

    public function addSocialLink(string $platform, string $username): void
    {
        $this->validateSocialPlatform($platform);
        $this->socialLinks[$platform] = $username;
        $this->changedFields['socialLinks'][$platform] = $username;
        $this->updateAuditTrail();
    }

    public function removeSocialLink(string $platform): void
    {
        unset($this->socialLinks[$platform]);
        $this->changedFields['socialLinks'][$platform] = null;
        $this->updateAuditTrail();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId()->toString(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'bio' => $this->getBio(),
            'website' => $this->getWebsite(),
            'photo' => $this->getPhoto(),
            'social_links' => $this->getSocialLinks(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
            'created_by' => $this->getCreatedBy()->toString(),
            'updated_by' => $this->getUpdatedBy()->toString(),
        ];
    }

    public function validateSocialPlatform(string $platform): void
    {
        if (! in_array($platform, self::ALLOWED_SOCIAL_PLATFORMS)) {
            throw new InvalidArgumentException("Invalid social platform: $platform");
        }
    }

    public function finalizeUpdate(): void
    {
        if (! empty($this->changedFields)) {
            $changesDTO = new AuthorStateDTO(
                name: $this->changedFields['name'] ?? null,
                email: $this->changedFields['email'] ?? null,
                bio: $this->changedFields['bio'] ?? null,
                website: $this->changedFields['website'] ?? null,
                photo: $this->changedFields['photo'] ?? null,
                socialLinks: $this->changedFields['socialLinks'] ?? null
            );

            $this->recordEvent(new AuthorUpdated($this->id, $changesDTO));
            $this->changedFields = [];
        }
    }

    private function recordEvent($event): void
    {
        $this->domainEvents[] = $event;
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}
