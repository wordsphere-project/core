<?php

namespace WordSphere\Core\Interfaces\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Domain\ContentManagement\ContentTypeRegistry;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ContentType;
use WordSphere\Core\Interfaces\Filament\Resolvers\ContentTypeRouteResolver;

use function __;
use function app;
use function request;

trait InteractsWithContentType
{
    public static function getNavigationLabel(): string
    {
        return __(self::getContentType()->pluralName);
    }

    public static function getLabel(): ?string
    {
        return __(self::getContentType()->singularName);
    }

    public static function getPluralLabel(): string
    {
        return __(self::getContentType()->pluralName);
    }

    public static function getContentType(): ?ContentType
    {

        return app(ContentTypeRegistry::class)->get(
            app(ContentTypeRouteResolver::class)->resolve(request())
        );
    }

    public static function getUrl(string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {

        if (! isset($parameters['contentType'])) {
            $parameters['contentType'] = app(ContentTypeRouteResolver::class)->resolve(request());
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant);
    }
}
