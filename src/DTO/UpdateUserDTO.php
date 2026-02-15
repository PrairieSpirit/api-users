<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateUserDTO
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    public int $id;

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
        int $id,
        string $login,
        string $phone,
        string $pass
    ) {
        $this->id = $id;
        $this->login = $login;
        $this->phone = $phone;
        $this->pass = $pass;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) ($data['id'] ?? 0),
            (string) ($data['login'] ?? ''),
            (string) ($data['phone'] ?? ''),
            (string) ($data['pass'] ?? ''),
        );
    }
}
