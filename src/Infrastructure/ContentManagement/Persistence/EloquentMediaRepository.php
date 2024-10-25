<?php

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence;

use WordSphere\Core\Domain\ContentManagement\Repositories\MediaRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Media;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\MediaAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentMedia;

class EloquentMediaRepository implements MediaRepositoryInterface
{
    public function findById(Id $id): ?Media
    {
        $media = EloquentMedia::query()->find($id->toInt());

        return $media !== null ? MediaAdapter::fromCurator($media) : null;

    }
}
