<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Entities;

use DateTimeImmutable;
use WordSphere\Core\Domain\ContentManagement\Events\PageCreated;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\Concerns\HasAuditTrail;
use WordSphere\Core\Domain\Shared\Concerns\HasFeaturedImage;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Legacy\Enums\ContentStatus;
use WordSphere\Core\Legacy\Enums\ContentVisibility;

class Page
{
    use HasAuditTrail;
    use HasFeaturedImage;

    private Uuid $id;

    private string $title;

    private Slug $slug;

    private string $path;

    private ?string $content;

    private ?string $excerpt;

    private array $customFields;

    private ?string $template;

    private ?string $redirectUrl;

    private ?int $sortOrder;

    private ContentStatus $status;

    private ContentVisibility $visibility;

    private ?DateTimeImmutable $publishedAt;

    private array $domainEvents = [];

    public function __construct(
        Uuid $id,
        string $title,
        Slug $slug,
        string $path,
        Uuid $createdBy,
        Uuid $updatedBy,
        ?string $content = null,
        ?string $excerpt = null,
        ?array $customFields = [],
        ?string $template = null,
        ?int $sortOrder = null,
        ?string $redirectUrl = null,
        ?Id $featuredImageId = null,
        ?string $featuredImageUrl = null,
        ContentStatus $status = ContentStatus::DRAFT,
        ContentVisibility $visibility = ContentVisibility::PUBLIC,
        ?DateTimeImmutable $publishedAt = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->path = $path;
        $this->createdBy = $createdBy;
        $this->updatedBy = $updatedBy;
        $this->content = $content;
        $this->excerpt = $excerpt;
        $this->customFields = $customFields;
        $this->template = $template;
        $this->sortOrder = $sortOrder;
        $this->redirectUrl = $redirectUrl;
        $this->status = $status;
        $this->visibility = $visibility;
        $this->publishedAt = $publishedAt;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->featuredImageId = $featuredImageId;
        $this->featuredImageUrl = $featuredImageUrl;

        if (! $createdAt && ! $updatedAt) {
            $this->initializeHasAuditTrail($createdBy);
        } else {
            $this->createdAt = $createdAt;
            $this->updatedAt = $updatedAt;
        }

        $this->domainEvents[] = new PageCreated($this->id);

    }

    public static function create(
        string $title,
        Slug $slug,
        string $path,
        Uuid $createdBy,
        ?string $content = null,
        ?string $excerpt = null,
        ?array $customFields = [],
        ?string $template = null,
        ?int $sortOrder = null,
        ?string $redirectUrl = null,
        ?Id $featuredImage = null,
    ): Page {
        return new self(
            id: Uuid::generate(),
            title: $title,
            slug: $slug,
            path: $path,
            createdBy: $createdBy,
            updatedBy: $createdBy,
            content: $content,
            excerpt: $excerpt,
            customFields: $customFields,
            template: $template,
            sortOrder: $sortOrder,
            redirectUrl: $redirectUrl,
            featuredImageId: $featuredImage,
            status: ContentStatus::DRAFT,
            visibility: ContentVisibility::PUBLIC,
        );
    }

    public function updateTitle(string $newTitle): void
    {
        $this->title = $newTitle;
        $this->updateAuditTrail();
    }

    public function updateSlug(Slug $newSlug): void
    {
        $this->slug = $newSlug;
        $this->updateAuditTrail();
    }

    public function updatePath(string $newPath): void
    {
        $this->path = $newPath;
        $this->updateAuditTrail();
    }

    public function updateContent(string $newContent): void
    {
        $this->content = $newContent;
        $this->updateAuditTrail();
    }

    public function updateExcerpt(string $newExcerpt): void
    {
        $this->excerpt = $newExcerpt;
        $this->updateAuditTrail();
    }

    public function updateCustomFields(array $newCustomFields): void
    {
        $this->customFields = $newCustomFields;
        $this->updateAuditTrail();
    }

    public function updateTemplate(string $newTemplate): void
    {
        $this->template = $newTemplate;
        $this->updateAuditTrail();
    }

    public function updateSortOrder(int $newSortOrder): void
    {
        $this->sortOrder = $newSortOrder;
        $this->updateAuditTrail();
    }

    public function updateRedirectUrl(string $newRedirectUrl): void
    {
        $this->redirectUrl = $newRedirectUrl;
        $this->updateAuditTrail();
    }

    public function updateStatus(ContentStatus $newStatus): void
    {
        $this->status = $newStatus;
        $this->updateAuditTrail();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getSlug(): Slug
    {
        return $this->slug;
    }

    public function getCreatedBy(): Uuid
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): Uuid
    {
        return $this->updatedBy;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function getCustomFields(): array
    {
        return $this->customFields;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function getStatus(): ContentStatus
    {
        return $this->status;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'path' => $this->path,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'customFields' => $this->customFields,
            'template' => $this->template,
            'featuredImageId' => $this->featuredImageId,
            'featuredImageUrl' => $this->featuredImageUrl,
            'sortOrder' => $this->sortOrder,
            'redirectUrl' => $this->redirectUrl,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'publishedAt' => $this->publishedAt,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}
