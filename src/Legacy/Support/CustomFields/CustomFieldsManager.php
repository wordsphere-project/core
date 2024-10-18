<?php

namespace WordSphere\Core\Legacy\Support\CustomFields;

use Exception;
use Filament\Forms\Components\Field;
use WordSphere\Core\Legacy\Contracts\CustomFieldsManagerContract;
use WordSphere\Core\Legacy\Enums\ResourceTab;

use function collect;

class CustomFieldsManager implements CustomFieldsManagerContract
{
    public function __construct(
        private CustomFieldsContainer $container,
    ) {}

    /**
     * @throws Exception
     */
    public function registerFields(string $resource, string $tab, ?CustomFieldsScope $scope = null, ?array $fields = null): void
    {

        if (! $scope) {
            $scope = BaseResourceCustomFieldsScope::make();
        }

        if (! $fields) {
            throw new Exception('No custom fields registered');
        }

        $this->container->items->add(
            item: new CustomFieldsRegistration(
                resource: $resource,
                tab: $tab,
                scope: $scope,
                fields: collect($fields)
            )
        );
    }

    public function container(): CustomFieldsContainer
    {
        return $this->container;
    }

    /**
     * @return null|array<int, Field>
     */
    public function getScopeSchema(string $resource, string $tab, ?CustomFieldsScope $scope = null): ?array
    {

        return $this->baseSchemaFilter(resource: $resource, scope: $scope)
            ->where('tab', $tab)
            ->pluck('fields')
            ->flatten(2)
            ->all();
    }

    /**
     * @return array<int, Field>|null
     */
    public function getNonSystemScopeSchema(string $resource, ?CustomFieldsScope $scope = null): ?array
    {
        return $this->baseSchemaFilter(resource: $resource, scope: $scope)
            ->whereNotIn('tab', [ResourceTab::cases()])
            ->pluck('fields')
            ->flatten(2)
            ->all();
    }

    private function baseSchemaFilter(string $resource, ?CustomFieldsScope $scope = null): mixed
    {
        if (! $scope) {
            $scope = BaseResourceCustomFieldsScope::make();
        }

        return $this->container->items
            ->where('resource', $resource)
            ->where('scope.key', $scope->key)
            ->where('scope.value', $scope->value);

    }
}
