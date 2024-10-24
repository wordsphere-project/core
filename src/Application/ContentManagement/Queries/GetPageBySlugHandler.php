<?php

namespace WordSphere\Core\Application\ContentManagement\Queries;

use WordSphere\Core\Domain\ContentManagement\Entities\Page;
use WordSphere\Core\Domain\ContentManagement\Repositories\PageRepositoryInterface;

readonly class GetPageBySlugHandler
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
    ) {}

    public function handle(GetPageBySlugQuery $query): ?Page
    {
        return $this->pageRepository->findBySlug($query->slug);
    }
}
