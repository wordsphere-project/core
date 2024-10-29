<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Types;

use WordSphere\Core\Legacy\Enums\ContentVisibility;

use function array_key_exists;

readonly class FilamentTypeData
{
    public function __construct(
        private string $singularName,
        private string $pluralName,
        private ?string $navigationGroup = null,
        private ?string $label = null,
        private ?string $icon = null,
        private ?string $description = null,
        private bool $showExcerpt = true,
        private bool $showContent = true,
        private bool $showVisibility = false,
        private ?int $defaultVisibility = ContentVisibility::PUBLIC->value,
        private bool $hasAuthor = false,
        private bool $hasFeaturedImage = false,
    ) {}

    public function getSingularName(): string
    {
        return $this->singularName;
    }

    public function getPluralName(): string
    {
        return $this->pluralName;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function showExcerpt(): bool
    {
        return $this->showExcerpt;
    }

    public function showContent(): bool
    {
        return $this->showContent;
    }

    public function showVisibility(): bool
    {
        return $this->showVisibility;
    }

    public function getDefaultVisibility(): int
    {
        return $this->defaultVisibility;
    }

    public function hasAuthor(): bool
    {
        return $this->hasAuthor;
    }

    public function hasFeaturedImage(): bool
    {
        return $this->hasFeaturedImage;
    }

    public function toArray(): array
    {
        return [
            'singularName' => $this->getSingularName(),
            'pluralName' => $this->getPluralName(),
            'navigationGroup' => $this->getNavigationGroup(),
            'label' => $this->getLabel(),
            'icon' => $this->getIcon(),
            'description' => $this->getDescription(),
            'showExcerpt' => $this->showExcerpt,
            'showContent' => $this->showContent,
            'showVisibility' => $this->showVisibility,
            'defaultVisibility' => $this->defaultVisibility,
            'hasAuthor' => $this->hasAuthor,
            'hasFeaturedImage' => $this->hasFeaturedImage,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            singularName: array_key_exists('singularName', $data) ? $data['singularName'] : '',
            pluralName: array_key_exists('pluralName', $data) ? $data['pluralName'] : '',
            navigationGroup: array_key_exists('navigationGroup', $data) ? $data['navigationGroup'] : '',
            label: array_key_exists('label', $data) ? $data['label'] : '',
            icon: array_key_exists('icon', $data) ? $data['icon'] : '',
            description: array_key_exists('description', $data) ? $data['description'] : '',
            showExcerpt: array_key_exists('showExcerpt', $data) ? $data['showExcerpt'] : true,
            showContent: array_key_exists('showContent', $data) ? $data['showContent'] : true,
            showVisibility: array_key_exists('showVisibility', $data) ? $data['showVisibility'] : false,
            defaultVisibility: array_key_exists('defaultVisibility', $data) ? $data['defaultVisibility'] : ContentVisibility::PUBLIC->value,
            hasAuthor: array_key_exists('author', $data) ? $data['author'] : false,
            hasFeaturedImage: array_key_exists('hasFeaturedImage', $data) ? $data['hasFeaturedImage'] : true,
        );
    }
}
