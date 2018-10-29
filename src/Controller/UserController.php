<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Exception\UserInvalidArgumentException;
use Symfony\Component\Form\FormError;


 /**
  * @IsGranted("ROLE_SUPER_ADMIN", message="Você não tem as permissões necessárias para acessar o módulo!")
  */
class UserController extends AbstractController
{
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

        $form = $this->createFormBuilder($user)
                     ->add('name', TextType::class, ['label' => 'Nome', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('username', TextType::class, ['label' => 'Usuário', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('plainPassword', RepeatedType::class, ['type' => PasswordType::class,
                     'first_options'  => array('label' => 'Senha', 'attr' => ['class' => 'form-control']),
                     'second_options' => array('label' => 'Repita sua senha', 'attr' => ['class' => 'form-control']),
                     ])
                     ->add('email', EmailType::class, ['label' => 'E-mail', 'attr' => ['class' => 'form-control']])
                     ->add('roleLevel', ChoiceType::class)
                     ->add('isActive', CheckboxType::class, ['label' => 'Ativo', 'required' => false])
                     ->add('roleLevel', ChoiceType::class, ['label' => 'Nível de acesso', 'required' => true, 'choices' => [
                         'Administrador' => 'admin',
                         'Super Administrador' => 'super_admin'
                     ], 'attr' => ['class' => 'form-control']])
                     ->add('save', SubmitType::class, ['label' => 'Adicionar', 'attr' => ['class' => 'btn btn-primary mt-3']])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            
            $user->setPassword($password);
            
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
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
        $form = $this->createFormBuilder($user)
                     ->add('name', TextType::class, ['label' => 'Nome', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('username', TextType::class, ['label' => 'Usuário', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('email', EmailType::class, ['label' => 'E-mail', 'attr' => ['class' => 'form-control']])
                     ->add('roleLevel', ChoiceType::class, ['label' => 'Nível de acesso', 'required' => true, 'choices' => [
                        'Administrador' => 'admin',
                        'Super Administrador' => 'super_admin'
                    ], 'attr' => ['class' => 'form-control']])
                     ->add('isActive', CheckboxType::class, ['label' => 'Ativo', 'required' => false])
                     ->add('save', SubmitType::class, ['label' => 'Salvar', 'attr' => ['class' => 'btn btn-primary mt-3']])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

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
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('user');
    }
}
