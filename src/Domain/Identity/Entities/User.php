<?php

namespace WordSphere\Core\Domain\Identity\Entities;

use WordSphere\Core\Domain\Shared\ValueObjects\Email;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

class User
{
    private readonly Id $id;

    private readonly Uuid $uuid;

    private ?string $name;

    private Email $email;

    public function __construct(
        Id $id,
        Uuid $uuid,
        Email $email,
        ?string $name = null
    ) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->name = $name;
        $this->email = $email;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function updateName(?string $name): void
    {
        $this->name = $name;
    }

    public function updateEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
