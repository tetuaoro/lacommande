<?php

namespace App\Security\Voter;

use App\Entity\Meal;
use App\Entity\Menu;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MenuVoter extends Voter
{
    public const CREATE = 'MENU_CREATE';
    public const EDIT = 'MENU_EDIT';
    public const VIEW = 'MENU_VIEW';
    public const DELETE = 'MENU_DELETE';

    private $security;

    public function __construct(Security $security, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->user = $userRepository;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::CREATE, self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Menu;
    }

    /**
     * Undocumented function.
     *
     * @param \App\Entity\Menu $subject
     * @param [type]           $attribute
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
                return $subject->getProvider() === $user->getProvider();

                break;
        }

        return false;
    }

    /* protected function check(string $mode, User $user, Meal $subject)
    {
        if ($this->security->isGranted('ROLE_SUBUSER')) {
            if (self::VALIDATE == $mode) {
                return $user->getSubuser()->getProvider() == $subject->getProvider() && array_key_exists(self::KEYMEAL, $user->getSubuser()->getRoles());
            }
            if (self::CREATE == $mode) {
                return array_key_exists(self::KEYMEAL, $user->getSubuser()->getRoles());
            }
        }

        return $user->getProvider() == $subject->getProvider();
    } */
}
