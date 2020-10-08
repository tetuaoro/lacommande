<?php

namespace App\Security\Voter;

use App\Entity\Meal;
use App\Entity\User;
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
    public const VALIDATE = 'MEAL_VALIDATE';

    public const KEYMEAL = 'meal-crud';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::VALIDATE, self::CREATE, self::VIEW])
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
            case self::VIEW:
                return $this->check(self::VALIDATE, $user, $subject);

                break;
            case self::VALIDATE:
                return $this->check(self::VALIDATE, $user, $subject);

                break;
            case self::CREATE:
                return $this->check(self::CREATE, $user, $subject);

                break;
        }

        return false;
    }

    protected function check(string $mode, User $user, Meal $subject)
    {
        if ($this->security->isGranted('ROLE_SUBUSER')) {
            if (self::VALIDATE == $mode) {
                return $user->getSubuser()->getProvider() == $subject->getProvider() && array_key_exists(self::KEYMEAL, $user->getSubuser()->getRoles());
            }
            if (self::CREATE == $mode) {
                return array_key_exists(self::KEYMEAL, $user->getSubuser()->getRoles());
            }
        }

        if (self::CREATE == $mode) {
            return $this->security->isGranted('ROLE_PROVIDER');
        }

        return $user->getProvider() == $subject->getProvider();
    }
}
