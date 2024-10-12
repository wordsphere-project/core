<?php

declare(strict_types=1);

use Filament\Forms\Components\Select;
use Symfony\Component\HttpFoundation\Response;
use WordSphere\Core\Filament\Resources\PageResource;
use WordSphere\Core\Filament\Resources\PageResource\Pages\ListPages;
use WordSphere\Core\Models\Page;
use WordSphere\Core\Settings\AppSettings;
use WordSphere\Core\Support\Theme\ThemeManager;
use WordSphere\Tests\TestCase;
use function WordSphere\Tests\livewire;

beforeEach(function () {
    /* @var TestCase $this */

    AppSettings::fake(
        values: [
            'theme' => 'wordsphere/orbit-theme'
        ],
        loadMissingValues: true
    );


});


describe('page admin', tests: function (): void {

    test('template selector has the templates as options', function(): void {

        $component = livewire(component: PageResource\Pages\CreatePage::class);

        $component->assertFormFieldExists('template', function (Select $input)  {
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
