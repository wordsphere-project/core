<?php

namespace WordSphere\Core\Interfaces\Http\Api\ContentManagement\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use WordSphere\Core\Application\ContentManagement\Queries\GetContentBySlugQuery;
use WordSphere\Core\Application\ContentManagement\Queries\GetContentsByTypeQuery;
use WordSphere\Core\Application\ContentManagement\Services\CachedContentQueryService;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Interfaces\Http\Api\ContentManagement\Resources\ContentResource;
use WordSphere\Core\Interfaces\Http\Api\Middleware\ValidateApiKey;

class ContentApiController extends Controller
{
    public function __construct(
        private CachedContentQueryService $contentQueryService,
    ) {
        $this->middleware(ValidateApiKey::class);
    }

    public function getByType(string $type, Request $request): JsonResponse
    {

        try {
            $query = new GetContentsByTypeQuery(
                type: TypeKey::fromString($type),
                page: (int) $request->get('page', 1),
                perPage: (int) $request->get('per_page', 10),
                orderBy: $request->get('order_by', 'created_at'),
                orderDirection: $request->get('order_direction', 'desc')
            );

            $contents = $this->contentQueryService->getContentsByType(
                query: $query,
            );

            return response()->json([
                'data' => ContentResource::collection($contents->items()),
                'meta' => [
                    'current_page' => $contents->currentPage(),
                    'last_page' => $contents->lastPage(),
                    'per_page' => $contents->perPage(),
                    'total' => $contents->total(),
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

    }

    public function getBySlug(string $type, string $slug): JsonResponse
    {
        $query = new GetContentBySlugQuery(
            type: TypeKey::fromString($type),
            slug: $slug
        );

        $content = $this->contentQueryService->getContentBySlug($query);

        if (! $content) {
            return response()->json(['error' => 'Content not found'], 404);
        }

        return response()->json([
            'data' => new ContentResource($content),
        ]);
    }
}
