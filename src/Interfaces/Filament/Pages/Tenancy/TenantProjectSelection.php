<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Pages\Tenancy;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Session\SessionManager;
use WordSphere\Core\Infrastructure\Tenancy\Persistence\Models\EloquentProject;
use WordSphere\Core\Infrastructure\Tenancy\Persistence\Models\EloquentTenant;

class TenantProjectSelection extends Page
{
    protected static string $view = 'wordsphere::filament.pages.tenancy.tenant-project-selection';

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $slug = 'tenant-project-selection';

    protected static ?string $title = 'Select Tenant & Project';

    protected static bool $shouldRegisterNavigation = false;

    public ?string $tenant_id = null;

    public ?string $project_id = null;

    private SessionManager $sessionManager;

    public function boot(SessionManager $sessionManager): void
    {
        $this->sessionManager = $sessionManager;
    }

    public function mount(): void
    {

        $tenantId = $this->sessionManager->get('current_tenant_id');
        $projectId = $this->sessionManager->get('current_project_id');

        if ($tenantId && EloquentTenant::query()->find($tenantId)) {
            $this->tenant_id = $tenantId;
        }

        if ($projectId && EloquentProject::query()->find($projectId)) {
            $this->project_id = $projectId;
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('tenant_id')
                ->label('Tenant')
                ->options(EloquentTenant::query()->pluck('name', 'id'))
                ->required()
                ->reactive()
                ->afterStateUpdated(fn (): null => $this->project_id = null),

            Select::make('project_id')
                ->label('Project')
                ->options(function ($get) {
                    if (! $get('tenant_id')) {
                        return [];
                    }

                    return EloquentProject::query()
                        ->where('tenant_id', $get('tenant_id'))
                        ->pluck('name', 'id');
                })
                ->required()
                ->visible(fn ($get) => filled($get('tenant_id'))),
        ]);
    }

    public function submit(): void
    {
        // Validate that selected tenant and project still exist
        $tenant = EloquentTenant::query()->find($this->tenant_id);
        $project = EloquentProject::query()->find($this->project_id);

        if (! $tenant || ! $project) {
            $this->addError('general', 'Selected tenant or project no longer exists.');

            return;
        }

        // Verify project belongs to tenant
        if ($project->tenant_id !== $tenant->id) {
            $this->addError('project_id', 'Selected project does not belong to the selected tenant.');

            return;
        }

        $this->sessionManager->put('current_tenant_id', $this->tenant_id);
        $this->sessionManager->put('current_project_id', $this->project_id);

        $this->redirect(filament()->getHomeUrl());
    }

    protected function getViewData(): array
    {
        $tenant = $this->tenant_id ? EloquentTenant::query()->find($this->tenant_id) : null;
        $project = $this->project_id ? EloquentProject::query()->find($this->project_id) : null;

        return [
            'tenant' => $tenant,
            'project' => $project,
            'tenantName' => $tenant?->name ?? 'No tenant selected',
            'projectName' => $project?->name ?? 'No project selected',
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }
}
