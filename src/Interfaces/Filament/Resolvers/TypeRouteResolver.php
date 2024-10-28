<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Resolvers;

use Illuminate\Http\Request;
use WordSphere\Core\Domain\Types\TypeRegistry;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;

use function json_decode;
use function request;

readonly class TypeRouteResolver
{
    public function __construct(
        private TypeRegistry $typeRegistry
    ) {}

    public function resolve(Request $request): ?string
    {
        $routeParameters = $request->route()->parameters();

        return $routeParameters['type'] ?? $this->resolveFromSnapshot($request);
    }

    private function resolveFromSnapshot(Request $request): ?string
    {
        return json_decode(request()->get('components')[0]['snapshot'], true)['data']['data'][0]['type'] ?? null;
    }

    public function validateContentType(string $key): bool
    {
        return $this->typeRegistry->get(TypeKey::fromString($key)) !== null;
    }
}
