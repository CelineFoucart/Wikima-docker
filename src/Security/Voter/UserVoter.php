<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public function __construct(
        private VoterHelper $voterHelper
    ) {  }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [VoterHelper::EDIT, VoterHelper::DELETE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case VoterHelper::EDIT:
                return $this->voterHelper->canModerate($user);
                break;
            case VoterHelper::DELETE:
                return $this->canDelete($user, $subject);
                break;
        }

        return false;
    }

    protected function canDelete(User $user, User $subject): bool
    {
        return $this->voterHelper->canModerate($user) && !in_array('ROLE_ADMIN', $subject->getRoles());
    }
}
