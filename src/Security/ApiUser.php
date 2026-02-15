<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class ApiUser implements UserInterface
{
    private string $username;
    private array $roles;

    public function __construct(string $username, array $roles = [])
    {
        $this->username = $username;
        $this->roles = $roles;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void {}
}

