<?php

namespace App\Security\Voter;

use App\Entity\Review;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ReviewVoter extends Voter
{
    private $security;

    public const REMOVE = 'REVIEW_REMOVE';

    public function __construct(Security $security) {
        $this->security = $security;
    }


    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::REMOVE])) {
            return false;
        }

        // only vote on `Review` objects
        if (!$subject instanceof Review) {
            return false;
        }

        return true;
    }


    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_MODERATEUR')) {
            return true;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::REMOVE:
                // logic to determine if the user can REMOVE
                return $user === $subject->getUser();
                break;
        }

        return false;
    }
}
