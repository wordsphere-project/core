<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Entities;

use DateTimeImmutable;
use InvalidArgumentException;
use WordSphere\Core\Domain\ContentManagement\Enums\ArticleStatus;
use WordSphere\Core\Domain\ContentManagement\Events\ArticleCreated;
use WordSphere\Core\Domain\ContentManagement\Events\ArticlePublished;
use WordSphere\Core\Domain\ContentManagement\Events\ArticleUnpublished;
use WordSphere\Core\Domain\ContentManagement\Events\ArticleUpdated;
use WordSphere\Core\Domain\ContentManagement\Exceptions\InvalidArticleStatusException;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\Concerns\HasAuditTrail;
use WordSphere\Core\Domain\Shared\Concerns\HasFeaturedImage;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

use function array_merge;

class Content
{
    use HasAuditTrail;
    use HasFeaturedImage;

    private Uuid $id;

    private string $title;

    private Slug $slug;

    private ?string $content;

    private ?string $excerpt;

    private array $customFields;

    private ArticleStatus $status;

    private ?DateTimeImmutable $publishedAt;

    private ?Author $author;

    private array $domainEvents = [];

    public function __construct(
        Uuid $id,
        string $title,
        Slug $slug,
        Uuid $createdBy,
        Uuid $updatedBy,
        ?Author $author = null,
        ?string $content = null,
        ?string $excerpt = null,
        ?array $customFields = [],
        ?Id $featuredImage = null,
        ArticleStatus $status = ArticleStatus::DRAFT,
        ?DateTimeImmutable $publishedAt = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->author = $author;
        $this->content = $content;
        $this->excerpt = $excerpt;
        $this->customFields = $customFields ?? [];
        $this->status = $status;
        $this->publishedAt = $publishedAt ?? null;
        $this->updateFeaturedImageId($featuredImage);

        $this->createdBy = $createdBy;
        $this->updatedBy = $updatedBy;

        if (! $createdAt && ! $updatedAt) {
            $this->initializeHasAuditTrail($createdBy);
        } else {
            $this->createdAt = $createdAt;
            $this->updatedAt = $updatedAt;
        }

        $this->ensureValidState();
        $this->domainEvents[] = new ArticleCreated($this->id);
    }

    public static function create(
        string $title,
        Slug $slug,
        Uuid $creator,
        ?string $content = null,
        ?string $excerpt = null,
        ?array $customFields = [],
        ?Id $featuredImage = null,
    ): self {
        return new self(
            id: Uuid::generate(),
            title: $title,
            slug: $slug,
            createdBy: $creator,
            updatedBy: $creator,
            content: $content,
            excerpt: $excerpt,
            customFields: $customFields,
            featuredImage: $featuredImage,
            status: ArticleStatus::DRAFT,
        );
    }

    public function update(
        Uuid $updater,
        string $title,
        ?string $content = '',
        ?string $excerpt = '',
        ?string $slugString = null,
        ?array $customFields = [],
        ?Author $author = null,
        ?Id $featuredImage = null,
    ): void {
        $this->title = $title;
        $this->content = $content;
        $this->excerpt = $excerpt;
        $this->slug = $slugString ? Slug::fromString($slugString) : $this->slug;
        if ($customFields !== null) {
            $this->customFields = array_merge($this->customFields, $customFields);
        }
        $this->author = $author;
        $this->updateFeaturedImageId($featuredImage, $updater);
        $this->domainEvents[] = new ArticleUpdated($this->id);
    }

    public function updateTitle(string $newTitle, Uuid $updater): void
    {
        $this->title = $newTitle;
        $this->updateAuditTrail($updater);
    }

    public function updateContent(?string $newContent, Uuid $updater): void
    {
        $this->content = $newContent;
        $this->updateAuditTrail($updater);
    }

    public function updateExcerpt(?string $newExcerpt, Uuid $updater): void
    {
        $this->excerpt = $newExcerpt;
        $this->updateAuditTrail($updater);
    }

    public function updateSlug(Slug $newSlug, Uuid $updater): void
    {
        $this->slug = $newSlug;
        $this->updateAuditTrail($updater);
    }

    public function updateCustomFields(?array $newCustomFields, Uuid $updater): void
    {
        if ($newCustomFields === null) {
            $this->customFields = [];
        } else {
            $this->customFields = array_merge($this->customFields, $newCustomFields);
        }
        $this->updateAuditTrail($updater);
        $this->domainEvents[] = new ArticleUpdated($this->id);

    }

    public function updateAuthor(?Author $newAuthor, Uuid $updater): void
    {
        $this->author = $newAuthor;
        $this->updateAuditTrail($updater);
        $this->domainEvents[] = new ArticleUpdated($this->id);
    }

    public function publish(Uuid $updater): void
    {
        if ($this->status->isPublished()) {
            throw new InvalidArticleStatusException(__('Article is already published'));
        }
        $this->status = ArticleStatus::PUBLISHED;
        $this->publishedAt = new DateTimeImmutable;
        $this->updateAuditTrail($updater);
        $this->domainEvents[] = new ArticlePublished($this->id);
    }

    public function unpublish(Uuid $updater): void
    {
        if (! $this->status->isPublished()) {
            throw new InvalidArticleStatusException(__('Cannot unpublish an article that is not published.'));
        }
        $this->status = ArticleStatus::DRAFT;
        $this->publishedAt = null;
        $this->updateAuditTrail($updater);
        $this->domainEvents[] = new ArticleUnpublished($this->id);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): Slug
    {
        return $this->slug;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function getCustomFields(): ?array
    {
        return $this->customFields;
    }

    public function getStatus(): ArticleStatus
    {
        return $this->status;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * @return array<string, string|array|null>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId()->toString(),
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
            'excerpt' => $this->getExcerpt(),
            'slug' => $this->getSlug()->toString(),
            'status' => $this->getStatus()->value,
            'customFields' => $this->customFields,
            'author' => $this->getAuthor()?->toArray(),
            'featuredImageId' => $this->getFeaturedImageId()?->toInt(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'createdBy' => $this->getCreatedBy()->toString(),
            'updatedAt' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
            'updatedBy' => $this->getUpdatedBy()->toString(),
            'publishedAt' => $this->getPublishedAt()?->format('Y-m-d H:i:s'),
        ];
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    private function ensureValidState(): void
    {
        if (empty($this->title)) {
            throw new InvalidArgumentException('Title cannot be empty');
        }
        if ($this->updatedAt < $this->createdAt) {
            throw new InvalidArgumentException('Updated at cannot be earlier than created at');
        }
        if ($this->publishedAt !== null && $this->publishedAt < $this->createdAt) {
            throw new InvalidArgumentException('Published at cannot be earlier than created at');
        }
    }
}
