<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\UserNewType;
use App\Form\User\UserEditType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
     * @Template("admin/user/index.html.twig")
     */
    public function index()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return [
            'controller_name' => 'UserController',
            'module_title' => 'Gerenciamento de usuários',
            'users' => $users
        ];
    }

    /**
     * @Route("/admin/user/new", name="user_new")
     * @Template("admin/user/new.html.twig")
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
        
        return [
            'module_title' => 'Adicionar usuário',
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/admin/user/edit/{id}"), methods={"GET","HEAD"})
     * @Template("admin/user/edit.html.twig")
     */
    public function edit(Request $request, User $user)
    {
        $form = $this->createForm(UserEditType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('user');
        }

        return [
            'module_title' => 'Editar usuário',
            'form' => $form->createView()
        ];
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
