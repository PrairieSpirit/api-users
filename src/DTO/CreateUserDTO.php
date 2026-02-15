<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 8)]
    public string $login;

    #[Assert\NotBlank]
    #[Assert\Length(max: 8)]
    public string $phone;

    #[Assert\NotBlank]
    #[Assert\Length(max: 8)]
    public string $pass;

    private function __construct(
        string $login,
        string $phone,
        string $pass
    ) {
        $this->login = $login;
        $this->phone = $phone;
        $this->pass  = $pass;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['login'] ?? ''),
            (string) ($data['phone'] ?? ''),
            (string) ($data['pass'] ?? ''),
        );
    }
}
