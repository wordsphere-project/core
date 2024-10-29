<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use InvalidArgumentException;
use WordSphere\Core\Application\ContentManagement\Contracts\ContentStatusServiceInterface;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Infrastructure\Shared\Events\LaravelEventDispatcher;

readonly class ContentStatusServiceFactory
{
    public function __construct(
        private ContentRepositoryInterface $articleRepository,
        private LaravelEventDispatcher $eventDispatcher,
    ) {}

    public function create(string $contentClass): ContentStatusServiceInterface
    {
        return match ($contentClass) {
            Content::class => new ContentStatusService($this->articleRepository, $this->eventDispatcher),
            default => throw new InvalidArgumentException("Unsupported content type: {$contentClass}"),
        };
    }
}
