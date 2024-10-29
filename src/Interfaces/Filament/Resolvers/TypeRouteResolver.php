<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Resolvers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use WordSphere\Core\Domain\Types\TypeRegistry;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;

use function request;
use function url;

readonly class TypeRouteResolver
{
    public function __construct(
        private TypeRegistry $typeRegistry
    ) {}

    public function resolve(Request $request): ?string
    {
        $routeParameters = $request->route()->parameters();

        return $routeParameters['type'] ?? $this->resolveFromPreviousRoute($request);
    }

    private function resolveFromPreviousRoute(Request $request): ?string
    {

        $type = request('type');

        if (isset($type)) {
            return $type;
        }

        $previousRoute = Route::getRoutes()->match(\Illuminate\Support\Facades\Request::create(url()->previous()));

        if ($previousRoute->parameter('type')) {
            $type = $previousRoute->parameter('type');
            $request->merge(['type' => $type]);

            return $type;
        }

        return null;

    }

    public function validateContentType(string $key): bool
    {
        return $this->typeRegistry->get(TypeKey::fromString($key)) !== null;
    }
}
