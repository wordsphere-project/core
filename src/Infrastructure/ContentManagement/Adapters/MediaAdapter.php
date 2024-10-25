<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Adapters;

use Awcodes\Curator\Models\Media as CuratorMedia;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Media;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentMedia;

class MediaAdapter
{
    public static function fromCurator(CuratorMedia|EloquentMedia $model, array|string $attributes = []): Media
    {
        $decodedAttributes = is_string($attributes) ? json_decode($attributes, true) : $attributes;

        return new Media(
            id: $model->id,
            width: $model->width ?? $decodedAttributes['width'],
            height: $model->height ?? $decodedAttributes['height'],
            curations: $decodedAttributes['curations'] ?? $model->extra_attributes['curations'] ?? [],
            exif: $decodedAttributes['exif'] ?? $model->extra_attributes['exif'] ?? [],
            /** @phpstan-ignore-next-line  */
            url: $model->url,
            /** @phpstan-ignore-next-line  */
            thumbnailUrl: $model->thumbnail_url,
            /** @phpstan-ignore-next-line  */
            mediumUrl: $model->medium_url,
            /** @phpstan-ignore-next-line  */
            largeUrl: $model->large_url,
            /** @phpstan-ignore-next-line  */
            sizeForHumans: $model->size_for_humans,
            /** @phpstan-ignore-next-line  */
            prettyName: $model->pretty_name
        );
    }

    public static function toCurator(Media $media): array
    {
        return [
            'id' => $media->id,
            'width' => $media->width,
            'height' => $media->height,
            'curations' => $media->curations,
            'exif' => $media->exif,
            'url' => $media->url,
            'thumbnail_url' => $media->thumbnailUrl,
            'medium_url' => $media->mediumUrl,
            'large_url' => $media->largeUrl,
            'size_for_humans' => $media->sizeForHumans,
            'pretty_name' => $media->prettyName,
        ];
    }
}
