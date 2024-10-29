<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Console\Commands\Api;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;
use WordSphere\Core\Infrastructure\Api\Persistence\Models\ApiKeyModel;
use WordSphere\Core\Infrastructure\Tenancy\Persistence\Models\EloquentProject;

class GenerateApiKey extends Command
{
    protected $signature = 'api:generate-key {name} {--tenant_id} {--project_id}';

    protected $description = 'Generate a new API key for a tenant';

    public function handle(): int
    {
        $key = Str::random(32);

        $tenantId = $this->option('tenant_id');
        $projectId = $this->option('project_id');

        if (! $tenantId || ! $projectId) {
            $project = EloquentProject::query()->first();
            $projectId = $project->id;
            $tenantId = $project->tenant_id;
        }

        ApiKeyModel::query()->create([
            'key' => $key,
            'name' => $this->argument('name'),
            'tenant_id' => $tenantId,
            'project_id' => $projectId,
            'active' => true,
        ]);

        $this->info("API key generated: $key");

        return CommandAlias::SUCCESS;
    }
}
