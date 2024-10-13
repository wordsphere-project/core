<?php

declare(strict_types=1);

use Filament\Forms\Components\Select;
use Symfony\Component\HttpFoundation\Response;
use WordSphere\Core\Filament\Resources\PageResource;
use WordSphere\Core\Filament\Resources\PageResource\Pages\ListPages;
use WordSphere\Core\Models\Page;
use WordSphere\Core\Support\Themes\ThemeManager;

use function WordSphere\Tests\livewire;

describe('page admin', tests: function (): void {

    test('it shows the custom fields group', function (): void {})->todo();

    test('it shows the content field by default', function (): void {
        $component = livewire(
            component: PageResource\Pages\CreatePage::class
        );

        $component->assertFormFieldExists(
            fieldName: 'content'
        );
    });

    test('it show the excerpt field if excerpt support is on', function (): void {
        $component = livewire(
            component: PageResource\Pages\CreatePage::class
        );
        $component->fillForm(
            state: [
                'excerptSupport' => true,
            ]
        );

        $component->assertFormFieldExists(
            fieldName: 'excerpt'
        );
    });

    test('it shows the excerpt field if the field has content', function (): void {})->todo();

    test('it does not show the excerpt field if excerpt support is off', function () {

        $component = livewire(component: PageResource\Pages\CreatePage::class);
        $component->assertFormFieldDoesNotExist('excerpt');
    })->skip();

    test('template selector has the templates as options', function (): void {

        $component = livewire(component: PageResource\Pages\CreatePage::class);

        $component->assertFormFieldExists('template', function (Select $input) {
            /** @var ThemeManager $themeManager */
            $themeManager = app()->make(
                abstract: ThemeManager::class
            );

            return $themeManager->getCurrentThemeTemplates() === $input->getOptions();
        });

    });

    it('can create', function (): void {

        $newData = Page::factory()->make();

        livewire(PageResource\Pages\CreatePage::class)
            ->fillForm(
                state: [
                    'title' => $newData->title,
                    'path' => $newData->path,
                    'content' => $newData->content,
                    'template' => $newData->template,
                    'status' => $newData->status,
                ]
            )
            ->call('create')
            ->assertHasNoFormErrors();

    });

    it('can render the create page', function () {
        $this->get(uri: PageResource::getUrl('create'))
            ->assertStatus(Response::HTTP_OK);
    });

    it('can render the pages admin', closure: function () {
        $response = $this->get(uri: PageResource::getUrl('index'));
        expect($response->status())
            ->toBe(Response::HTTP_OK);

    });

    it('can list pages', function (): void {
        $pages = Page::factory()
            ->count(10)
            ->create();

        livewire(ListPages::class)
            ->assertCanSeeTableRecords($pages);

    });

});
