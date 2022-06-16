<?php

namespace App\EntityEventListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function prePersist(User $user)
    {
        $this->hashPassword($user)
            ->normalizeFirstName($user)
            ->normalizeLastName($user)
        ;
    }

    public function preUpdate(User $user)
    {
        $this->hashPassword($user)
            ->normalizeFirstName($user)
            ->normalizeLastName($user)
        ;
    }

    private function hashPassword(User $user) 
    {
        if ($user->getPlainTextPassword()) {
            $user->setPassword($this->hasher->hashPassword($user, $user->getPlainTextPassword()));
            $user->setPlainTextPassword(null);
        }
        return $this;
    }
    
    private function normalizeFirstName(User $user)
    {
        if ($user->getFirstName()) {
            $user->setFirstName(ucwords($user->getFirstName()));
        }
        return $this;
    }

    private function normalizeLastName(User $user)
    {
        if ($user->getLastName()) {
            $user->setLastName(mb_strtoupper($user->getLastName()));
        }
        return $this;
    }
}