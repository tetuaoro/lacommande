<?php

namespace App\Security\Voter;

use App\Entity\Command;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CommandVoter extends Voter
{
    public const CREATE = 'COMMAND_CREATE';
    public const EDIT = 'COMMAND_EDIT';
    public const VIEW = 'COMMAND_VIEW';
    public const DELETE = 'COMMAND_DELETE';
    public const VALIDATE = 'COMMAND_VALIDATE';

    protected const KEYCOMMAND = 'command-crud';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::VIEW, self::VALIDATE])
            && $subject instanceof \App\Entity\Command;
    }

    /**
     * Undocumented function.
     *
     * @param [type]              $attribute
     * @param \App\Entity\Command $subject
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
                return $this->check(self::VIEW, $user, $subject);

                break;
            case self::VALIDATE:
                return $this->check(self::VALIDATE, $user, $subject);

                break;
        }

        return false;
    }

    protected function check(string $mode, User $user, Command $subject)
    {
        if ($this->security->isGranted('ROLE_SUBUSER')) {
            if (self::VIEW == $mode) {
                return $subject->findProvider($user->getSubuser()->getProvider());
            }
            if (self::VALIDATE == $mode) {
                return array_key_exists(self::KEYCOMMAND, $user->getSubuser()->getRoles()) && $subject->findProvider($user->getSubuser()->getProvider());
            }
        }

        return $subject->findProvider($user->getProvider());
    }
}
