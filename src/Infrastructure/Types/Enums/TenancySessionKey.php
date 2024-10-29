<?php

namespace WordSphere\Core\Infrastructure\Types\Enums;

enum TenancySessionKey: string
{
    case CURRENT_TENANT_ID = 'current_tenant_id';
    case CURRENT_PROJECT_ID = 'current_project_id';

}
