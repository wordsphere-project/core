<?php

declare(strict_types=1);

namespace WordSphere\Core\Legacy\Contracts;

use WordSphere\Core\Legacy\Support\CustomFields\CustomFieldsContainer;
use WordSphere\Core\Legacy\Support\CustomFields\CustomFieldsScope;

interface CustomFieldsManagerContract
{
    public function registerFields(string $resource, string $tab, ?CustomFieldsScope $scope = null, ?array $fields = null): void;

    /**
     * Returns the Manager container.
     */
    public function container(): CustomFieldsContainer;

    public function getScopeSchema(string $resource, string $tab, ?CustomFieldsScope $scope = null): ?array;

    public function getNonSystemScopeSchema(string $resource, ?CustomFieldsScope $scope = null): ?array;
}
