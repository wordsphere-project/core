<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use WordSphere\Core\Application\ContentManagement\Queries\GetPageByPathHandler;
use WordSphere\Core\Application\ContentManagement\Queries\GetPageByPathQuery;
use WordSphere\Core\Application\ContentManagement\Queries\GetPageBySlugHandler;
use WordSphere\Core\Application\ContentManagement\Queries\GetPageBySlugQuery;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Interfaces\Http\Resources\PageResource;

class PageController
{
    public function __construct(
        private readonly GetPageByPathHandler $getPageByPathHandler,
        private readonly GetPageBySlugHandler $getPageBySlugHandler,
    ) {}

    public function getByPath(Request $request, string $path): JsonResponse
    {

        $query = new GetPageByPathQuery($path);
        $page = $this->getPageByPathHandler->handle($query);

        if ($page === null) {
            return new JsonResponse([
                'message' => 'Page not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            new PageResource($page),
            Response::HTTP_OK
        );

    }

    public function getBySlug(Request $request, string $slug): JsonResponse
    {

        $query = new GetPageBySlugQuery(Slug::fromString($slug));
        $page = $this->getPageBySlugHandler->handle($query);

        if ($page === null) {
            return new JsonResponse([
                'message' => 'Page not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            new PageResource($page),
            Response::HTTP_OK
        );

    }
}
