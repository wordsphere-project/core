<?php

namespace WordSphere\Core\Interfaces\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Domain\Types\Entities\Type;
use WordSphere\Core\Domain\Types\TypeRegistry;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Interfaces\Filament\Resolvers\TypeRouteResolver;
use WordSphere\Core\Interfaces\Filament\Types\FilamentTypeData;

use function __;
use function app;
use function request;

trait InteractsWithType
{
    public static function getNavigationLabel(): string
    {
        return self::getModelPluralLabel();
    }

    public static function getLabel(): ?string
    {
        return __(self::getInterfaceData()->getSingularName());
    }

    public static function getModelLabel(): string
    {
        return self::getInterfaceData()->getSingularName();
    }

    public static function getModelPluralLabel(): string
    {
        return self::getInterfaceData()->getPluralName();
    }

    public static function getInterfaceData(): ?FilamentTypeData
    {
        return FilamentTypeData::fromArray(self::getType()->getInterfaceData());

    }

    public static function getType(): ?Type
    {
        return app(TypeRegistry::class)->get(
            TypeKey::fromString(app(TypeRouteResolver::class)->resolve(request()))
        );
    }

    public static function getUrl(string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {

        if (! isset($parameters['type'])) {
            $parameters['type'] = app(TypeRouteResolver::class)->resolve(request());
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant);
    }
}
