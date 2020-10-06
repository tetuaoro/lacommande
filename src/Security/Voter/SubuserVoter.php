<?php

namespace App\Security\Voter;

use App\Entity\Subuser;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SubuserVoter extends Voter
{
    public const CREATE = 'SUBUSER_CREATE';
    public const EDIT = 'SUBUSER_EDIT';
    public const VIEW = 'SUBUSER_VIEW';
    public const DELETE = 'SUBUSER_DELETE';
    public const MANAGE = 'SUBUSER_MANAGE';

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
        return in_array($attribute, [self::CREATE, self::EDIT, self::VIEW, self::DELETE, self::MANAGE])
            && $subject instanceof Subuser;
    }

    /**
     * Undocumented function.
     *
     * @param [type]           $attribute
     * @param \App\Entity\Subuser $subject
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
                return $subject === $user->getSubuser();

                break;
            case self::DELETE:
                return $subject === $user->getSubuser();

                break;
            case self::VIEW:
                return $subject === $user;

                break;
            case self::MANAGE:
                return $subject->getProvider() === $user->getProvider();

                break;
        }

        return false;
    }
}
