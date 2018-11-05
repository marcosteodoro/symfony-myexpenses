<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    /**
     * @Route("/admin/expense", name="expense")
     */
    public function index()
    {
        return $this->render('expense/index.html.twig', [
            'controller_name' => 'ExpenseController',
            'module_title' => 'Gerenciamento de despesas'
        ]);
    }
}
