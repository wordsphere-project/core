<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Entities;

use DateTimeImmutable;
use InvalidArgumentException;
use WordSphere\Core\Domain\ContentManagement\Enums\ArticleStatus;
use WordSphere\Core\Domain\ContentManagement\Events\ArticlePublished;
use WordSphere\Core\Domain\ContentManagement\Events\ArticleUnpublished;
use WordSphere\Core\Domain\ContentManagement\Events\ArticleUpdated;
use WordSphere\Core\Domain\ContentManagement\Exceptions\InvalidArticleStatusException;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;

use function array_merge;

class Article
{
    private array $domainEvents = [];

    public function __construct(
        private readonly ArticleId $id,
        private string $title,
        private Slug $slug,
        private ?string $content,
        private ?string $excerpt,
        private ?array $data,
        private ArticleStatus $status,
        public readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
        private ?DateTimeImmutable $publishedAt = null,
    ) {
        $this->ensureValidState();
    }

    public static function create(
        string $title,
        Slug $slug,
        ?string $content = null,
        ?string $excerpt = null,
        ?array $data = [],
    ): self {

        $now = new DateTimeImmutable;

        return new self(
            id: ArticleId::generate(),
            title: $title,
            slug: $slug,
            content: $content,
            excerpt: $excerpt,
            data: $data,
            status: ArticleStatus::DRAFT,
            createdAt: $now,
            updatedAt: $now,
        );

    }

    public function update(
        string $title,
        ?string $content = '',
        ?string $excerpt = '',
        ?string $slugString = null,
        ?array $data = [],
    ): void {
        $this->title = $title;
        $this->content = $content;
        $this->excerpt = $excerpt;
        $this->slug = Slug::fromString($slugString ?? $title);
        $this->data = $data;
        $this->updatedAt = new DateTimeImmutable;

        $this->domainEvents[] = new ArticleUpdated($this->id);
    }

    public function updateTitle(string $newTitle): void
    {
        $this->title = $newTitle;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function updateContent(?string $newContent): void
    {
        $this->content = $newContent;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function updateExcerpt(?string $newExcerpt): void
    {
        $this->excerpt = $newExcerpt;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function updateSlug(Slug $newSlug): void
    {
        $this->slug = $newSlug;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function updateData(?array $newData): void
    {
        if ($newData === null) {
            // If null is passed, clear the existing data
            $this->data = [];
        } else {
            // Merge the new data with existing data
            $this->data = array_merge($this->data, $newData);
        }
        $this->updatedAt = new DateTimeImmutable();

    }

    public function publish(): void
    {
        if (! $this->status->isDraft()) {
            throw new InvalidArticleStatusException('Cannot publish an article that is not in draft status.');
        }
        $this->status = ArticleStatus::PUBLISHED;
        $this->updatedAt = new DateTimeImmutable;
        $this->publishedAt = new DateTimeImmutable;
        $this->domainEvents[] = new ArticlePublished($this->id);
    }

    public function unpublish(): void
    {
        if (! $this->status->isPublished()) {
            throw new InvalidArticleStatusException('Cannot unpublish an article that is not published.');
        }
        $this->status = ArticleStatus::DRAFT;
        $this->updatedAt = new \DateTimeImmutable;
        $this->domainEvents[] = new ArticleUnpublished($this->id);
    }

    public function getId(): ArticleId
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

    public function getData(): ?array
    {
        return $this->data;
    }

    public function getStatus(): ArticleStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
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
            'status' => $this->getStatus()->toString(),
            'data' => $this->getData(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
            'published_at' => $this->getPublishedAt()?->format('Y-m-d H:i:s'),
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
