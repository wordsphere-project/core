<?php

namespace WordSphere\Core\Domain\Identity\Entities;

use WordSphere\Core\Domain\Identity\ValueObjects\UserId;
use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;
use WordSphere\Core\Domain\Shared\ValueObjects\Email;

class User
{
    private UserId $id;

    private UserUuid $uuid;

    private ?string $name;

    private Email $email;

    public function __construct(
        UserId $id,
        UserUuid $uuid,
        Email $email,
        ?string $name = null
    ) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->name = $name;
        $this->email = $email;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getUuid(): UserUuid
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
