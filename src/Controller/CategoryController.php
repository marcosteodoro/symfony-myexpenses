<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Utils\CategoryRenderUtils;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;  
use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Category;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index()
    {
        $entityRepository = $this->getDoctrine()->getRepository(Category::class);

        $categories = ($this->isGranted('ROLE_SUPER_ADMIN')) ? $entityRepository->findAll() : $this->getUser()->getCategories();

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategotyController',
            'module_title' => 'Gerenciamento de categorias',
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/new")
     *
     * @param Request $request
     */
    public function new(Request $request)
    {
        $category = new Category();

        $categoryRenderUtils = new CategoryRenderUtils();

        $usersChoice = $categoryRenderUtils->getUsersChoice(
            $this->isGranted('ROLE_SUPER_ADMIN'), 
            $this->getUser(), 
            $this->getDoctrine()->getRepository(User::class)
        );

        $form = $this->createFormBuilder($category)
                                    ->add('name', TextType::class, ['label' => 'Nome', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                                    ->add('description', TextType::class, ['label' => 'Descrição', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                                    ->add('user', EntityType::class, [
                                        'label' => 'Usuário',
                                        'class' => User::class,
                                        'choices' => $usersChoice,
                                        'attr' => ['class' => 'form-control']
                                    ])
                                    ->add('save', SubmitType::class, ['label' => 'Adicionar', 'attr' => ['class' => 'btn btn-primary mt-3']])
                                    ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category');
        }

        return $this->render('category/new.html.twig', [
            'module_title' => 'Adicionar categoria',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/edit/{id}"), methods={"GET","HEAD"})
     */
    public function edit(Request $request, Category $category)
    {
        $formBuilder = $this->createFormBuilder($category)
                     ->add('name', TextType::class, ['label' => 'Nome', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('description', TextType::class, ['label' => 'Descrição', 'attr' => ['class' => 'form-control', 'autocomplete' => false]]);
        
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {

            $usersChoice = (new CategoryRenderUtils)->getUsersChoice(
                true, 
                $this->getUser(), 
                $this->getDoctrine()->getRepository(User::class)
            );

            $formBuilder->add('user', EntityType::class, [
                    'label' => 'Usuário',
                    'class' => User::class,
                    'choices' => $usersChoice,
                    'attr' => ['class' => 'form-control']
            ]);
        }
            
        $form = $formBuilder->add('save', SubmitType::class, ['label' => 'Salvar', 'attr' => ['class' => 'btn btn-primary mt-3']])
             ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('category');
        }

        return $this->render('category/edit.html.twig', [
            'module_title' => 'Editar categoria',
            'form' => $form->createView()
        ]);
    }

    /** 
     * @Route("/category/delete/{id}"), methods=({"DELETE"})
     */
    public function delete(Request $resquest, Category $category)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        $response = new Response();
        $response->setStatusCode(204, 'No content');

        return $response->send();
    }
}
