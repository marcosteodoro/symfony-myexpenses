<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserNewType;
use App\Form\UserEditType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Exception\UserInvalidArgumentException;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;


 /**
  * @IsGranted("ROLE_SUPER_ADMIN", message="Você não tem as permissões necessárias para acessar o módulo!")
  */
class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/user", name="user")
     */
    public function index()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('admin/user/index.html.twig', [
            'controller_name' => 'UserController',
            'module_title' => 'Gerenciamento de usuários',
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/user/new", name="user_new")
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $form = $this->createForm(UserNewType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            
            $user->setPassword($password);
            
            try {
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return $this->redirectToRoute('user');
            } catch (UserInvalidArgumentException $e) {
                $formError = new FormError($e->getMessage());
                $form->addError($formError);
            }
        }
        
        return $this->render('admin/user/new.html.twig', [
            'module_title' => 'Adicionar usuário',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/edit/{id}"), methods={"GET","HEAD"})
     */
    public function edit(Request $request, User $user)
    {
        $form = $this->createForm(UserEditType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('user');
        }

        return $this->render('admin/user/edit.html.twig', [
            'module_title' => 'Editar usuário',
            'form' => $form->createView()
        ]);
    }

    /** 
     * @Route("/admin/user/delete/{id}"), methods=({"DELETE"})
     */
    public function delete(Request $resquest, User $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('user');
    }
}
