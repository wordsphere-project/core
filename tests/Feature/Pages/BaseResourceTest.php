<?php

declare(strict_types=1);

use Filament\Forms\Components\Select;
use Symfony\Component\HttpFoundation\Response;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentPage;
use WordSphere\Core\Infrastructure\Identity\Persistence\UserModel;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource\Pages\CreatePage;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource\Pages\ListPages;
use WordSphere\Core\Legacy\Support\Themes\ThemeManager;

use function WordSphere\Tests\livewire;

describe('page admin', tests: function (): void {

    test('it shows the custom fields group', function (): void {})->todo();

    test('it shows the content field by default', function (): void {
        $component = livewire(
            component: CreatePage::class
        );

        $component->assertFormFieldExists(
            fieldName: __('content')
        );
    });

    test('it show the excerpt field if excerpt support is on', function (): void {
        $component = livewire(
            component: CreatePage::class
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

    test('it does not show the excerpt field if excerpt support is off', function (): void {

        $component = livewire(component: CreatePage::class);
        $component->assertFormFieldDoesNotExist('excerpt');
    })->skip();

    test('template selector has the templates as options', function (): void {

        $component = livewire(component: CreatePage::class);

        $component->assertFormFieldExists('template', function (Select $input) {
            /** @var ThemeManager $themeManager */
            $themeManager = app()->make(
                abstract: ThemeManager::class
            );

            return $themeManager->getCurrentThemeTemplates() === $input->getOptions();
        });

    });

    it('can create', function (): void {

        /** @var UserModel $user */
        $user = UserModel::factory()->create();

        /** @var EloquentPage $newData */
        $newData = EloquentPage::factory()
            ->for($user, 'createdBy')
            ->for($user, 'updatedBy')
            ->make();

        $this->actingAs($user);

        livewire(CreatePage::class)
            ->fillForm(
                state: [
                    'title' => $newData->title,
                    'path' => 'dsds',
                    'slug' => 'dscd',
                    'content' => $newData->content,
                    'template' => $newData->template,
                    'status' => $newData->status,
                    'customFields' => [],
                ]
            )->call('create')
            ->assertHasNoFormErrors();

    });

    it('can render the create page', function (): void {
        $this->get(uri: PageResource::getUrl('create'))
            ->assertStatus(Response::HTTP_OK);
    });

    it('can render the pages admin', closure: function (): void {
        $response = $this->get(uri: PageResource::getUrl('index'));
        expect($response->status())
            ->toBe(Response::HTTP_OK);

    });

    it('can list pages', function (): void {

        /** @var UserModel $user */
        $user = UserModel::factory()->create();

        $pages = EloquentPage::factory()
            ->for($user, 'createdBy')
            ->for($user, 'updatedBy')
            ->count(10)
            ->create();

        livewire(ListPages::class)
            ->assertCanSeeTableRecords($pages);

    });

});
