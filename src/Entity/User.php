<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[UniqueEntity(
    fields: ['login', 'pass'],  // валідатор дивиться на властивість PHP
    message: 'Ця комбінація логіну та пароля вже існує.'
)]#[ORM\UniqueConstraint(
    name: 'uniq_users_login_pas',
    columns: ['login', 'pas']  // це назви колонок у базі
)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 8, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 8)]
    private string $login;

    #[ORM\Column(type: 'string', length: 8, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 8)]
    private string $phone;

    #[ORM\Column(name: 'pas', type: 'string', length: 8, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 8)]
    private string $pass;

    // ---------- getters / setters ----------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function setPass(string $pass): self
    {
        $this->pass = $pass;
        return $this;
    }
}
