<?php

namespace WordSphere\Core\Application\ContentManagement\Contracts;

use WordSphere\Core\Application\ContentManagement\Commands\ChangeContentStatusCommand;

interface ContentStatusServiceInterface
{
    public function execute(ChangeContentStatusCommand $command): void;
}
