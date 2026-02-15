<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreateUserDTO;
use App\DTO\UpdateUserDTO;
use App\Entity\User;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Exception\ValidationException;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
//use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
//use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
//use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
//use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserService
{
    public function __construct(
        private UserRepository $repository,
        private Security $security,
        private ValidatorInterface $validator
    ) {}

    // --------------------
    // CONTEXT
    // --------------------

    public function getCurrentUserId(): ?int
    {
        $user = $this->security->getUser();

        return match (true) {
            $user === null => null,
            $user->getUserIdentifier() === 'root' => null,
            default => (int) $user->getUserIdentifier(),
        };
    }

    public function getCurrentUserRole(): string
    {
        $user = $this->security->getUser();

        return match (true) {
            $user === null => 'anonymous',
            $user->getUserIdentifier() === 'root' => 'root',
            default => 'user',
        };
    }


    // --------------------
    // ACCESS CONTROL
    // --------------------

    public function assertCanView(int $targetUserId): void
    {
        $role = $this->getCurrentUserRole();
        $currentId = $this->getCurrentUserId();

        match ($role) {
            'anonymous' => throw new UnauthorizedException(),
            'root' => null,
            'user' => $currentId === $targetUserId
                ? null
                : throw new ForbiddenException(),
            default => throw new ForbiddenException(),
        };
    }


    public function assertCanUpdate(int $targetUserId): void
    {
        // update = ті самі правила, що й view
        $this->assertCanView($targetUserId);
    }

    public function assertCanDelete(int $targetUserId): void
    {
        $role = $this->getCurrentUserRole();

        match ($role) {
            'anonymous' => throw new UnauthorizedException(),
            'root' => null,
            'user' => throw new ForbiddenException(),
            default => throw new ForbiddenException(),
        };
    }

    // --------------------
    // QUERIES
    // --------------------

    public function getUser(int $id): User
    {
        $this->assertCanView($id);

        return $this->repository->findOrFail($id);
    }

    // --------------------
    // COMMANDS
    // --------------------

    public function create(CreateUserDTO $dto): User
    {
        $this->validate($dto);

        if ($this->repository->existsByLoginAndPass($dto->login, $dto->pass)) {
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

        $this->assertCanUpdate($dto->id);

        $user = $this->repository->findOrFail($dto->id);

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
        $this->assertCanDelete($id);

        $user = $this->repository->findOrFail($id);

        $this->repository->remove($user);
    }

    private function validate(object $object): void
    {
        $violations = $this->validator->validate($object);

        if (count($violations) > 0) {
            throw ValidationException::fromViolations($violations);
        }
    }
}

