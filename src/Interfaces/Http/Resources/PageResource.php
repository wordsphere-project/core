<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method getId()
 * @method getTitle()
 * @method getContent()
 * @method getExcerpt()
 * @method getPath()
 * @method getSlug()
 * @method getStatus()
 * @method getCreatedAt()
 * @method getUpdatedAt()
 * @method getCustomFields()
 * @method getFeaturedImage()
 * @method getFeaturedImageId()
 * @method getFeaturedImageUrl()
 */
class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getId()->toString(),
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
            'excerpt' => $this->getExcerpt(),
            'path' => $this->getPath(),
            'slug' => $this->getSlug()->toString(),
            'customFields' => $this->getCustomFields(),
            'featuredImage' => $this->when($this->getFeaturedImageId(), [
                'id' => $this->getFeaturedImageId()->toInt(),
                'url' => $this->getFeaturedImageUrl(),
            ], null),
        ];
    }
}
