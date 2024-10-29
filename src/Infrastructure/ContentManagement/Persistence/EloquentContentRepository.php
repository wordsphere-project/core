<?php

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence;

use Illuminate\Support\Facades\DB;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Media;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\ContentAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;

use function collect;

class EloquentContentRepository implements ContentRepositoryInterface
{
    public function nextIdentity(): Uuid
    {
        return Uuid::generate();
    }

    public function findById(Uuid $id): ?Content
    {
        return self::findByUuid($id);
    }

    public function findByUuid(Uuid $uuid): ?Content
    {
        $eloquentContent = ContentModel::query()
            ->with(['media' => function ($query): void {
                $query->orderBy('order');
            }])
            ->find($uuid);

        return $eloquentContent ? ContentAdapter::toDomain($eloquentContent) : null;
    }

    public function findBySlug(Slug $slug): ?Content
    {
        $eloquentContent = ContentModel::query()
            ->with(['media' => function ($query): void {
                $query->orderBy('order');
            }])
            ->where('slug', $slug->toString())
            ->first();

        return $eloquentContent ? ContentAdapter::toDomain($eloquentContent) : null;
    }

    public function save(Content $content): void
    {
        $eloquentContent = ContentAdapter::toEloquent($content);

        DB::transaction(function () use ($eloquentContent, $content): void {
            $this->updateModelFromEntity($eloquentContent, $content);
            $eloquentContent->save();
            $this->saveMediaRelations($eloquentContent, $content);
            $eloquentContent->refresh();
        });

    }

    public function delete(Uuid $id): void
    {
        ContentModel::destroy($id->toString());
    }

    public function isSlugUnique(Slug $slug): bool
    {
        return ! ContentModel::query()
            ->where('slug', $slug->toString())
            ->exists();
    }

    private function updateModelFromEntity(ContentModel $eloquentContent, Content $content): void
    {

        $eloquentContent->id = $content->getId();
        $eloquentContent->type = $content->getType();
        $eloquentContent->title = $content->getTitle();
        $eloquentContent->slug = $content->getSlug();
        $eloquentContent->content = $content->getContent();
        $eloquentContent->excerpt = $content->getExcerpt();
        $eloquentContent->status = $content->getStatus()->toString();
        $eloquentContent->created_at = $content->getCreatedAt();
        $eloquentContent->updated_at = $content->getUpdatedAt();
        $eloquentContent->published_at = $content->getPublishedAt();
        $eloquentContent->created_by = $content->getCreatedBy();
        $eloquentContent->updated_by = $content->getUpdatedBy();
        $eloquentContent->featured_image_id = $content->getFeaturedImage()?->getId();

    }

    private function saveMediaRelations(ContentModel $eloquentContent, Content $content): void
    {
        $mediaItems = collect($content->getMedia())->map(function (Media $media, $index) {

            return [
                'media_id' => $media->id,
                'order' => $index,
                'attributes' => json_encode([
                    'width' => $media->width,
                    'height' => $media->height,
                    'curations' => $media->curations,
                    'exif' => $media->exif,
                    'largeUrl' => $media->largeUrl,
                    'mediumUrl' => $media->mediumUrl,
                    'thumbnailUrl' => $media->thumbnailUrl,
                    'prettyName' => $media->prettyName,
                    'sizeForHumans' => $media->sizeForHumans,
                ]),
            ];
        })->toArray();

        $eloquentContent->media()->detach();
        $eloquentContent->media()->sync($mediaItems);

    }
}
