<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HashPasswordListener
{

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function hashPass(string $plainText)
    {
        return $this->hasher->hashPassword(new User(), $plainText);
    }
}