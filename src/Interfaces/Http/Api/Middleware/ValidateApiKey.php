<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Http\Api\Middleware;

use Closure;
use Illuminate\Http\Request;
use WordSphere\Core\Infrastructure\Api\Services\ApiKeyService;

readonly class ValidateApiKey
{
    public function __construct(
        private ApiKeyService $apiKeyService
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {

        $apiKey = $request->header('X-API-Key');

        if (! $apiKey || ! $this->apiKeyService->isValidKey($apiKey)) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        $this->apiKeyService->setCurrentTenant($apiKey);

        return $next($request);

    }
}
