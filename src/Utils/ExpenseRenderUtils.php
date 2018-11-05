<?php

namespace App\Utils;

use App\Entity\User;

class ExpenseRenderUtils
{
    /**
     * Método responsável por retornar as despesas vinculadas ao usuário logado de acordo com as categorias vinculadas ao mesmo
     *
     * @param User $loggedUser
     * @return array
     */
    public static function getUserExpenses(User $loggedUser)
    {
        $userExpenses = [];
        
        foreach ($loggedUser->getCategories() as $category) {
            foreach ($category->getExpenses() as $expense) {
                $userExpenses[] = $expense;
            }
        }

        return $userExpenses;
    }
}
