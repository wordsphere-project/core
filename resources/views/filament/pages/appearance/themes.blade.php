<x-filament-panels::page>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($this->themes as $theme)
            <div class="p-4 border rounded-md bg-white flex flex-col items-stretch h-48" key="{{ $theme['name'] }}">
                <div class="flex flex-col text-sm flex-1">
                   <span class="font-bold">
                       {{ $theme['name'] }}
                   </span>
                    <span class="text-black/60 dark:text-white/60">
                        {{ $theme['description'] }}
                    </span>
                </div>
                <div class="w-full">
                    {{ ($this->activateThemeAction)(['namespace' => $theme['name']]) }}
                </div>
            </div>
        @endforeach
        </div>
    <x-filament-actions::modals />
</x-filament-panels::page>

