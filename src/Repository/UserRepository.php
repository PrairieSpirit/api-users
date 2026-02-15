<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

final class UserRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $em
    ) {
        parent::__construct($registry, User::class);
    }

    public function findOrFail(int $id): User
    {
        $user = $this->find($id);

        if (!$user) {
            throw new NotFoundException();
        }

        return $user;
    }

    public function existsByLoginAndPass(
        string $login,
        string $pass,
        ?int $excludeId = null
    ): bool {
        $qb = $this->createQueryBuilder('u')
            ->select('1')
            ->where('u.login = :login')
            ->andWhere('u.pass = :pass')
            ->setParameter('login', $login)
            ->setParameter('pass', $pass)
            ->setMaxResults(1);

        if ($excludeId !== null) {
            $qb->andWhere('u.id != :id')
               ->setParameter('id', $excludeId);
        }

        return (bool) $qb->getQuery()->getOneOrNullResult();
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}
