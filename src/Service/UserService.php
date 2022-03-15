<?php

namespace App\Service;

use Symfony\Contracts\Translation\TranslatorInterface;

final class UserService
{
    private array $availableRoles = [
        'User' => 'ROLE_USER',
        'Editor' => 'ROLE_EDITOR',
        'Administrator' => 'ROLE_ADMIN'
    ];

    public function __construct(
        private TranslatorInterface $translator
    ) {   
    }

    /**
     * Get a list of available roles
     */ 
    public function getAvailableRoles(): array
    {
        return $this->availableRoles;
    }

    public function formatRoles(array $roles): string
    {
        $userRoles = [];

        foreach ($this->availableRoles as $key => $role) {
            if (in_array($role, $roles)) {
                $userRoles[] =$this->translator->trans($key);
            }
        }

        return join(', ', $userRoles);
    }
}