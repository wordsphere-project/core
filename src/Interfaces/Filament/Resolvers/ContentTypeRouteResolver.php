<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Resolvers;

use Illuminate\Http\Request;
use WordSphere\Core\Domain\ContentManagement\ContentTypeRegistry;

use function json_decode;
use function request;

readonly class ContentTypeRouteResolver
{
    public function __construct(
        private ContentTypeRegistry $contentTypeRegistry
    ) {}

    public function resolve(Request $request): ?string
    {
        $routeParameters = $request->route()->parameters();

        return $routeParameters['contentType'] ?? $this->resolveFromSnapshot($request);
    }

    private function resolveFromSnapshot(Request $request): ?string
    {
        return json_decode(request()->get('components')[0]['snapshot'], true)['data']['data'][0]['type'] ?? null;
    }

    public function validateContentType(string $contentType): bool
    {
        return $this->contentTypeRegistry->get($contentType) !== null;
    }
}
