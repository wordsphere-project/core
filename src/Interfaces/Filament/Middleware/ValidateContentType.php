<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use WordSphere\Core\Domain\ContentManagement\ContentTypeRegistry;

readonly class ValidateContentType
{
    public function __construct(private ContentTypeRegistry $contentTypeRegistry) {}

    public function handle(Request $request, Closure $next)
    {
        $contentType = $request->route('contentType');

        if (! $contentType) {
            return $next($request);
        }

        // Validate the content type exists
        if (! $this->contentTypeRegistry->get($contentType)) {
            Log::warning('Invalid content type requested', [
                'requested_type' => $contentType,
                'available_types' => array_keys($this->contentTypeRegistry->all()),
            ]);
            abort(404, 'Content type not found');
        }

        return $next($request);
    }
}
