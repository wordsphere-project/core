<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Http\Api\ContentManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Media;

/**
 * @property mixed $id
 * @property mixed $type
 * @property mixed $title
 * @property mixed $slug
 * @property mixed $content
 * @property mixed $excerpt
 * @property mixed $custom_fields
 * @property mixed $featuredImage
 * @property mixed $created_at
 * @property mixed $updated_at
 * @property mixed $published_at
 * @property mixed $media
 */
class ContentResource extends JsonResource
{
    public function toArray($request): array
    {

        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'customFields' => $this->custom_fields,
            'featuredImage' => $this->when($this->featuredImage, function () {
                return $this->transformMedia($this->featuredImage);
            }),
            'media' => $this->when(
                $this->media,
                fn () => $this->media->map(
                    fn ($mediaItem) => $this->transformMedia($mediaItem)
                )
            ),
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
            'publishedAt' => $this->published_at?->toIso8601String(),
        ];
    }

    protected function transformMedia($mediaModel): array
    {
        // Create Media value object from model
        $media = new Media(
            id: $mediaModel->id,
            width: $mediaModel->width,
            height: $mediaModel->height,
            curations: $mediaModel->curations ?? [],
            exif: $mediaModel->exif ?? [],
            url: $mediaModel->url,
            thumbnailUrl: $mediaModel->thumbnail_url,
            mediumUrl: $mediaModel->medium_url,
            largeUrl: $mediaModel->large_url,
            sizeForHumans: $mediaModel->size_for_humans,
            prettyName: $mediaModel->pretty_name,
        );

        return $media->toArray();
    }
}
