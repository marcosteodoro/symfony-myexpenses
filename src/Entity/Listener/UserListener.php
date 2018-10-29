<?php

namespace App\Entity\Listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\User;
use App\Entity\Exception\UserInvalidArgumentException;

class UserListener
{
    public function prePersist(User $user, LifecycleEventArgs $args)
    {
        $user = $args->getObject();
        $entityManager = $args->getObjectManager();
        
        $usersInDatabase = $entityManager->getRepository(User::class)
                                         ->findByUsernameOrEmail($user);

        if (count($usersInDatabase) > 0) {
            throw new UserInvalidArgumentException('Usuário ou e-mail já cadastrados!', 400);
        }
    }
}
