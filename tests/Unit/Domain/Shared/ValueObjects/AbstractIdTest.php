<?php

use Ramsey\Uuid\Uuid;
use WordSphere\Core\Domain\Shared\ValueObjects\AbstractId;

class ConcreteId extends AbstractId {}

it('can create id from valid uuid', function (): void {

    $uuid = Uuid::uuid4()->toString();
    $id = ConcreteId::fromString($uuid);

    expect($id)->toBeInstanceOf(ConcreteId::class)
        ->and($id->toString())->toBe($uuid);

});
