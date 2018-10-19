<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'module_title' => 'Gerenciamento de usuários',
            'users' => $users
        ]);
    }

    /**
     * @Route("/user/new", name="user_new")
     */
    public function new(Request $request)
    {
        return new Response("New User");
    }
}
