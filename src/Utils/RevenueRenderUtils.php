<?php

namespace App\Utils;

use App\Entity\User;

class RevenueRenderUtils
{
    /**
     * Método responsável por retornar as receitas vinculadas ao usuário logado de acordo com as categorias vinculadas ao mesmo
     *
     * @param User $loggedUser
     * @return array
     */
    public static function getUserRevenues(User $loggedUser)
    {
        $userRevenues = [];
        
        foreach ($loggedUser->getCategories() as $category) {
            foreach ($category->getRevenues() as $revenue) {
                $userRevenues[] = $revenue;
            }
        }

        return $userRevenues;
    }
}
