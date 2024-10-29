<x-filament-panels::page>
    <div class="mb-4">
        @if($tenant)
            <p>Current Tenant: {{ $tenantName }}</p>
        @endif

        @if($project)
            <p>Current Project: {{ $projectName }}</p>
        @endif
    </div>

    <form wire:submit="submit">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-4">
            Select
        </x-filament::button>
    </form>
</x-filament-panels::page>
