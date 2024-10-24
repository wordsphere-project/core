<?php

namespace WordSphere\Core\Application\ContentManagement\Queries;

use WordSphere\Core\Domain\ContentManagement\Entities\Page;
use WordSphere\Core\Domain\ContentManagement\Repositories\PageRepositoryInterface;

readonly class GetPageByPathHandler
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
    ) {}

    public function handle(GetPageByPathQuery $query): ?Page
    {
        return $this->pageRepository->findByPath($query->path);
    }
}
