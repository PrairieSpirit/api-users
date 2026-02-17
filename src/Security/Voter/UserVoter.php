<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Security\ApiUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class UserVoter extends Voter
{
    public const VIEW = 'USER_VIEW';
    public const UPDATE = 'USER_UPDATE';
    public const DELETE = 'USER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
                self::VIEW,
                self::UPDATE,
                self::DELETE,
            ], true) && $subject instanceof User;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $user = $token->getUser();

        if (!$user instanceof ApiUser) {
            return false; // anonymous
        }

        /** @var User $subject */
        $targetUser = $subject;

        // ROOT має повний доступ
        if (in_array('ROLE_ROOT', $user->getRoles(), true)) {
            return true;
        }

        // USER може працювати тільки зі своїм акаунтом
        if (in_array('ROLE_USER', $user->getRoles(), true)) {
            return match ($attribute) {
                self::VIEW, self::UPDATE =>
                    $user->getUserIdentifier() === (string) $targetUser->getId(),

                self::DELETE => false,
            };
        }

        return false;
    }
}
