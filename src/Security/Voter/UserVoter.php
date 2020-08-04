<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const CREATE = 'USER_CREATE';
    public const EDIT = 'USER_EDIT';
    public const VIEW = 'USER_VIEW';
    public const DELETE = 'USER_DELETE';

    private $security;
    private $user;

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
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user_ = $token->getUser();
        $user = $this->user->findOneBy(['username' => $user_->getUsername()]);
        // if the user is anonymous, do not grant access
        if (!$user_ instanceof UserInterface || !$user) {
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
                return $subject === $user;

                break;
        }

        return false;
    }
}