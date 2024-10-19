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

it('can generate a new id', function (): void {
    $id = ConcreteId::generate();
    expect($id)->toBeInstanceOf(ConcreteId::class)
        ->and(Uuid::isValid($id->toString()))->toBeTrue();
});

it('cannot create id from invalid uuid', function (): void {
    ConcreteId::fromString('invalid-uuid');
})->throws(InvalidArgumentException::class);

it('considers ids with same value equal', function (): void {
    $uuid = Uuid::uuid4()->toString();
    $id1 = ConcreteId::fromString($uuid);
    $id2 = ConcreteId::fromString($uuid);
    expect($id1->equals($id2))->toBeTrue();
});

it('considers ids with different value not equal', function (): void {
    $id1 = ConcreteId::generate();
    $id2 = ConcreteId::generate();
    expect($id1->equals($id2))->toBeFalse();
});

it('can be converted to string', function (): void {
    $uuid = Uuid::uuid4()->toString();
    $id = ConcreteId::fromString($uuid);
    expect((string) $id)->toBe($uuid);
});
