<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Entities;

use InvalidArgumentException;
use WordSphere\Core\Domain\Shared\Concerns\HasAuditTrail;
use WordSphere\Core\Domain\Shared\Concerns\HasFeaturedImage;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

class Author
{
    use HasAuditTrail;
    use HasFeaturedImage;

    private Uuid $id;

    private string $name;

    private string $email;

    private ?string $bio;

    private ?string $website;

    private array $socialLinks;

    private const ALLOWED_SOCIAL_PLATFORMS = [
        'twitter',
        'linkedin',
        'facebook',
        'instagram',
        'github',
        'pinkary',
    ];

    public function __construct(
        Uuid $id,
        string $name,
        string $email,
        Uuid $createdBy,
        Uuid $updatedBy,
        ?string $bio = null,
        ?string $website = null,
        ?Id $featuredImage = null,
        ?array $socialLinks = [],
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->bio = $bio;
        $this->website = $website;
        $this->socialLinks = $socialLinks;
        $this->updateFeaturedImage($featuredImage);
        $this->socialLinks = $socialLinks;

        $this->createdBy = $createdBy;
        $this->updatedBy = $updatedBy;

        $this->initializeHasAuditTrail($createdBy);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
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

    public function getSocialLinks(): array
    {
        return $this->socialLinks;
    }

    public function updateName(string $name, Uuid $updater): void
    {

        if (empty(trim($name))) {
            throw new InvalidArgumentException('Author name cannot be empty.');
        }

        $this->name = $name;
        $this->updateAuditTrail($updater);
    }

    public function updateEmail(string $email, Uuid $updater): void
    {

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        $this->email = $email;
        $this->updateAuditTrail($updater);
    }

    public function updateBio(?string $bio, Uuid $updater): void
    {
        $this->bio = $bio;
        $this->updateAuditTrail($updater);
    }

    public function updateWebsite(?string $website, Uuid $updater): void
    {
        if (! $website !== null && ! filter_var($website, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid website URL.');
        }
        $this->website = $website;
        $this->updateAuditTrail($updater);
    }

    public function updateSocialLinks(array $socialLinks, Uuid $updater): void
    {
        foreach ($socialLinks as $platform => $username) {
            $this->validateSocialPlatform($platform);
        }

        $this->socialLinks = $socialLinks;
        $this->updateAuditTrail($updater);
    }

    public function addSocialLink(string $platform, string $username, Uuid $updater): void
    {
        $this->validateSocialPlatform($platform);
        $this->socialLinks[$platform] = $username;
        $this->updateAuditTrail($updater);
    }

    public function removeSocialLink(string $platform, Uuid $updater): void
    {
        unset($this->socialLinks[$platform]);
        $this->updateAuditTrail($updater);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId()->toString(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'bio' => $this->getBio(),
            'website' => $this->getWebsite(),
            'socialLinks' => $this->getSocialLinks(),
            'featuredImageId' => $this->getFeaturedImage()?->toInt(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
            'created_by' => $this->getCreatedBy()->toString(),
            'last_updated_by' => $this->getUpdatedBy()->toString(),
        ];
    }

    public function validateSocialPlatform(string $platform): void
    {
        if (! in_array($platform, self::ALLOWED_SOCIAL_PLATFORMS)) {
            throw new InvalidArgumentException("Invalid social platform: $platform");
        }
    }
}
