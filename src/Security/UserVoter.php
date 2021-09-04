<?php

namespace App\Security;

use App\Entity\Client;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const VIEW   = 'view';
    const EDIT   = 'edit';

    protected function supports(string $attribute, $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // only vote on `User` objects
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $sessionClient = $token->getUser();

        if (!$sessionClient instanceof Client) {
            // the user must be logged in; if not, deny access
            return false;
        }
        // you know $subject is a User object, thanks to `supports()`
        /** @var User $user */
        $user = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $sessionClient);
            case self::EDIT:
                return $this->canEdit($user, $sessionClient);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canView(User $user, Client $sessionUser): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($user, $sessionUser)) {
            return true;
        }
        return false;
    }

    private function canEdit(User $user, Client $sessionUser): bool
    {
        // Can edit if
        return $sessionUser === $user->getClient();
    }
}
