<?php

namespace App\Security\Voter;

use App\Entity\Meal;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MealVoter extends Voter
{
    public const CREATE = 'MEAL_CREATE';
    public const EDIT = 'MEAL_EDIT';
    public const VIEW = 'MEAL_VIEW';
    public const DELETE = 'MEAL_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::CREATE, self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Meal;
    }

    /**
     * Undocumented function.
     *
     * @param \App\Entity\Meal $subject
     * @param mixed            $attribute
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var \App\Entity\User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::CREATE:
                return $this->security->isGranted('ROLE_PROVIDER');

                break;
            case self::EDIT:
                return $subject->getProvider() === $user->getProvider();

                break;
            case self::DELETE:
                return $subject->getProvider() === $user->getProvider();

                break;
            case self::VIEW:
                return $subject->getProvider() === $user->getProvider() || !$subject->getIsDelete();

                break;
        }

        return false;
    }
}
