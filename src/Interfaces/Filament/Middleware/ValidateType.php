<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use WordSphere\Core\Domain\Types\TypeRegistry;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;

readonly class ValidateType
{
    public function __construct(private TypeRegistry $typeRegistry) {}

    public function handle(Request $request, Closure $next)
    {
        $type = $request->route('type');

        if (! $type) {
            return $next($request);
        }

        // Validate the content type exists
        if (! $this->typeRegistry->get(TypeKey::fromString($type))) {
            Log::warning('Invalid content type requested', [
                'requested_type' => $type,
                'available_types' => array_keys($this->typeRegistry->all()),
            ]);
            abort(404, 'Content type not found');
        }

        return $next($request);
    }
}
