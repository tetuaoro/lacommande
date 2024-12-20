<?php

namespace App\Security\Voter;

use App\Entity\Provider;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProviderVoter extends Voter
{
    public const CREATE = 'PROVIDER_CREATE';
    public const EDIT = 'PROVIDER_EDIT';
    public const VIEW = 'PROVIDER_VIEW';
    public const DELETE = 'PROVIDER_DELETE';

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
            && $subject instanceof Provider;
    }

    /**
     * Undocumented function.
     *
     * @param [type]               $attribute
     * @param \App\Entity\Provider $subject
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
                return $subject === $user->getProvider();

                break;
            case self::DELETE:
                return $subject === $user->getProvider();

                break;
            case self::VIEW:
                return $subject === $user->getProvider();

                break;
        }

        return false;
    }
}
