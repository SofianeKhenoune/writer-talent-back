<?php

namespace App\Security\Voter;

use App\Entity\Post;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PostVoter extends Voter
{   
    public const UPDATE = 'POST_EDIT';
    public const REMOVE = 'POST_REMOVE';
    public const SETSTATUS = 'POST_SET_STATUS';
    public const READ = 'POST_READ';


    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }
    


    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::REMOVE, self::UPDATE, self::SETSTATUS, self::READ] )) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Post) {
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

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::REMOVE:
                // logic to determine if the user can REMOVE
                return $user === $subject->getUser();
                break;
            case self::UPDATE:
                // logic to determine if the user can UPDATE
                return $user === $subject->getUser();
                break;
            case self::SETSTATUS:
                // logic to determine if the user can UPDATE
                return $user === $subject->getUser();
                break;
            case self::READ:
                // logic to determine if the user can READ
                return $user === $subject->getUser();
                break;
        }

        return false;
    }
}
