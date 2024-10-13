<?php

declare(strict_types=1);

namespace WordSphere\Core\Contracts;

use WordSphere\Core\Support\CustomFields\CustomFieldsContainer;
use WordSphere\Core\Support\CustomFields\CustomFieldsScope;

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
