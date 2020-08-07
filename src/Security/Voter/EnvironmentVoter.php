<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EnvironmentVoter extends Voter
{
    public const MODE = 'MODE_DEV';

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::MODE]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ('dev' == $_ENV['APP_ENV']) {
            return true;
        }

        return false;
    }
}
