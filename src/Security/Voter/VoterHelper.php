<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;

class VoterHelper
{
    public const EDIT = 'edit';

    public const DELETE = 'delete';

    public const VIEW = 'view';

    public function canModerate(User $user): bool
    {
        return in_array("ROLE_ADMIN", $user->getRoles());
    }

    /**
     * @param User $user
     * @param Comment|Article $subject
     * 
     * @return bool
     */
    public function canEdit(User $user, $subject): bool
    {
        if ($this->canModerate($user)) {
            return true;
        }
        
        return $user->getId() === $subject->getAuthor()->getId();
    }

    /**
     * @param User $user
     * @param Comment|Article $subject
     * 
     * @return bool
     */
    public function canDelete(User $user, $subject): bool
    {
        return $this->canEdit($user, $subject);
    }
}