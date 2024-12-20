<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Types\Persistence\Repositories;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Domain\Types\Entities\Type;
use WordSphere\Core\Domain\Types\Enums\RelationType;
use WordSphere\Core\Domain\Types\Repositories\TypeRepositoryInterface;
use WordSphere\Core\Domain\Types\ValueObjects\AllowedRelation;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Infrastructure\Types\Persistence\Models\AllowedRelationModel;
use WordSphere\Core\Infrastructure\Types\Persistence\Models\TypeModel;

class EloquentTypeRepository implements TypeRepositoryInterface
{
    private bool $loadingRelations = false;

    public function findAll(Uuid $tenantId, Uuid $projectId): array
    {
        return TypeModel::query()
            ->where('tenant_id', $tenantId->toString())
            ->where('project_id', $projectId->toString())
            ->orderBy('key')
            ->get()
            ->map(fn (TypeModel $model) => $this->toDomainEntity($model))
            ->all();
    }

    public function findById(Uuid $id, Uuid $tenantId, Uuid $projectId): ?Type
    {
        $model = TypeModel::query()
            ->where('id', $id->toString())
            ->where('tenant_id', $tenantId->toString())
            ->where('project_id', $projectId->toString())
            ->first();

        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findByKey(TypeKey $key, Uuid $tenantId, Uuid $projectId): ?Type
    {
        $model = TypeModel::query()
            ->where('key', $key->toString())
            ->where('tenant_id', $tenantId->toString())
            ->where('project_id', $projectId->toString())
            ->first();

        return $model ? $this->toDomainEntity($model) : null;
    }

    public function save(Type $type): void
    {
        $model = TypeModel::query()
            ->where('key', $type->getKey()->toString())
            ->where('tenant_id', $type->getTenantId()->toString())
            ->where('project_id', $type->getProjectId()->toString())
            ->first() ?? new TypeModel;

        $model->fill([
            'id' => $type->getId()->toString(),
            'key' => $type->getKey()->toString(),
            'entity_class' => $type->getEntityClass(),
            'tenant_id' => $type->getTenantId()->toString(),
            'project_id' => $type->getProjectId()->toString(),
            'interface_data' => $type->getInterfaceData(),
            'custom_fields' => $type->getCustomFields(),
        ]);

        $model->save();

        // Save allowed relations
        foreach ($type->getAllowedRelations() as $relation) {
            AllowedRelationModel::query()->updateOrCreate(
                [
                    'source_type_id' => $type->getId()->toString(),
                    'name' => $relation->getName(),
                    'tenant_id' => $type->getTenantId()->toString(),
                    'project_id' => $type->getProjectId()->toString(),
                ],
                [
                    'target_type_id' => $relation->getTargetType()->getId()->toString(),
                    'relation_type' => $relation->getRelationType()->value,
                    'is_required' => $relation->isRequired(),
                    'min_items' => $relation->getMinItems(),
                    'max_items' => $relation->getMaxItems(),
                    'inverse_relation_name' => $relation->getInverseRelationName(),
                ]
            );
        }
    }

    public function delete(Type $type): void
    {
        TypeModel::query()->where('id', $type->getId()->toString())
            ->where('tenant_id', $type->getTenantId()->toString())
            ->where('project_id', $type->getProjectId()->toString())
            ->delete();
    }

    private function toDomainEntity(TypeModel $model, bool $whitRelations = true): Type
    {
        $type = new Type(
            Uuid::fromString($model->id),
            TypeKey::fromString($model->key),
            $model->entity_class,
            Uuid::fromString($model->tenant_id),
            Uuid::fromString($model->project_id)
        );

        if ($model->interface_data) {
            $type->addInterfaceData($model->interface_data);
        }

        if ($model->custom_fields) {
            $type->addCustomFieldsData($model->custom_fields);
        }

        if ($whitRelations && ! $this->loadingRelations) {
            $this->loadingRelations = true;
            foreach ($model->allowedRelations as $relationModel) {

                if ($model->id === $relationModel->target_type_id) {
                    continue;
                }

                $targetType = $this->findById(
                    Uuid::fromString($relationModel->target_type_id),
                    Uuid::fromString($model->tenant_id),
                    Uuid::fromString($model->project_id)
                );

                if ($targetType) {
                    $relation = new AllowedRelation(
                        $relationModel->name,
                        $type,
                        $targetType,
                        RelationType::from($relationModel->relation_type),
                        $relationModel->is_required,
                        $relationModel->min_items,
                        $relationModel->max_items,
                        $relationModel->inverse_relation_name
                    );

                    $type->addAllowedRelation($relation);
                }
            }
        }

        return $type;
    }
}
