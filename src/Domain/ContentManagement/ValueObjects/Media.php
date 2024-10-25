<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\ValueObjects;

readonly class Media
{
    public function __construct(
        public int $id,
        public int $width,
        public int $height,
        public array $curations,
        public array $exif,
        public string $url,
        public string $thumbnailUrl,
        public string $mediumUrl,
        public string $largeUrl,
        public string $sizeForHumans,
        public string $prettyName,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            width: $data['width'],
            height: $data['height'],
            curations: $data['curations'] ?? [],
            exif: $data['exif'] ?? [],
            url: $data['url'],
            thumbnailUrl: $data['thumbnailUrl'],
            mediumUrl: $data['mediumUrl'],
            largeUrl: $data['largeUrl'],
            sizeForHumans: $data['sizeForHumans'],
            prettyName: $data['prettyName'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'width' => $this->width,
            'height' => $this->height,
            'curations' => $this->curations,
            'exif' => $this->exif,
            'url' => $this->url,
            'thumbnailUrl' => $this->thumbnailUrl,
            'mediumUrl' => $this->mediumUrl,
            'largeUrl' => $this->largeUrl,
            'sizeForHumans' => $this->sizeForHumans,
            'prettyName' => $this->prettyName,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }
}
