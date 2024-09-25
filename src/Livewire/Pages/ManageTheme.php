<?php

declare(strict_types=1);

namespace WordSphere\Core\Livewire\Pages;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ManageTheme extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationLabel = 'Hello';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                components: [
                    TextInput::make('title'),
                    MarkdownEditor::make('content'),
                ]
            )
            ->statePath('data');
    }

    public function render(): View
    {
        return view('wordsphere::livewire.pages.manage-theme');
    }
}
