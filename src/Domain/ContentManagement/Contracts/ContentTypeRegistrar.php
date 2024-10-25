<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Contracts;

interface ContentTypeRegistrar
{
    public function register(): void;
}
