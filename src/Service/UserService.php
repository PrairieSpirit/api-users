<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreateUserDTO;
use App\DTO\UpdateUserDTO;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserService
{
    public function __construct(
        private UserRepository $repository,
        private Security $security,
        private ValidatorInterface $validator,
    ) {}

    // --------------------
    // QUERIES
    // --------------------

    public function getUser(int $id): User
    {
        $user = $this->repository->findOrFail($id);

        if (!$this->security->isGranted(UserVoter::VIEW, $user)) {
            throw new AccessDeniedException();
        }

        return $user;
    }

    // --------------------
    // COMMANDS
    // --------------------

    public function create(CreateUserDTO $dto): User
    {
        $this->validate($dto);

        if ($this->repository->existsByLoginAndPass(
            $dto->login,
            $dto->pass
        )) {
            throw new ValidationException(
                'User already exists',
                ['login' => ['User already exists']]
            );
        }

        $user = (new User())
            ->setLogin($dto->login)
            ->setPhone($dto->phone)
            ->setPass($dto->pass);

        $this->validate($user);

        $this->repository->save($user);

        return $user;
    }

    public function update(UpdateUserDTO $dto): User
    {
        $this->validate($dto);

        $user = $this->repository->findOrFail($dto->id);

        if (!$this->security->isGranted(UserVoter::UPDATE, $user)) {
            throw new AccessDeniedException();
        }

        if ($this->repository->existsByLoginAndPass(
            $dto->login,
            $dto->pass,
            $dto->id
        )) {
            throw new ValidationException(
                'User already exists',
                ['login' => ['User already exists']]
            );
        }

        $user
            ->setLogin($dto->login)
            ->setPhone($dto->phone)
            ->setPass($dto->pass);

        $this->validate($user);

        $this->repository->save($user);

        return $user;
    }

    public function delete(int $id): void
    {
        $user = $this->repository->findOrFail($id);

        if (!$this->security->isGranted(UserVoter::DELETE, $user)) {
            throw new AccessDeniedException();
        }

        $this->repository->remove($user);
    }

    // --------------------
    // VALIDATION
    // --------------------

    private function validate(object $object): void
    {
        $violations = $this->validator->validate($object);

        if (count($violations) > 0) {
            throw ValidationException::fromViolations($violations);
        }
    }
}
