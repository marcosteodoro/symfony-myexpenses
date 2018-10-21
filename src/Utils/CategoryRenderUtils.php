<?php

namespace App\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\User;
use App\Repository\UserRepository;

class CategoryRenderUtils
{
    public function getUsersChoice($isSuperAdmin, User $loggedUser, UserRepository $userRepository)
    {
        if ($isSuperAdmin) {
            return $userRepository->findAll();
        }
        
        return [$loggedUser];      
    }
}
