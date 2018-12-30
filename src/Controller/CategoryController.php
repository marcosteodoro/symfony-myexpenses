<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\Category;
use App\Form\Category\NewType;
use App\Form\Category\EditType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;  
use App\Utils\CategoryRenderUtils;
use Doctrine\ORM\EntityManagerInterface;

class CategoryController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/category", name="category")
     * @Template("admin/category/index.html.twig")
     */
    public function index()
    {
        $entityRepository = $this->entityManager->getRepository(Category::class);

        $categories = ($this->isGranted('ROLE_SUPER_ADMIN')) ? $entityRepository->findAll() : $this->getUser()->getCategories();

        return [
            'controller_name' => 'CategotyController',
            'module_title' => 'Gerenciamento de categorias',
            'categories' => $categories
        ];
    }

    /**
     * @Route("/admin/category/new"), name="category_new"
     * @Template("admin/category/new.html.twig")
     */
    public function new(Request $request)
    {
        $category = new Category;

        $categoryRenderUtils = new CategoryRenderUtils();

        $usersChoice = $categoryRenderUtils->getUsersChoice(
            $this->isGranted('ROLE_SUPER_ADMIN'),
            $this->getUser(),
            $this->entityManager->getRepository(User::class)
        );
        
        $form = $this->createForm(NewType::class, $category, ['usersChoice' => $usersChoice]);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('category');
        }
        
        return [
            'module_title' => 'Adicionar categoria',
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/admin/category/edit/{id}"), methods={"GET","HEAD"})
     * @Template("admin/category/edit.html.twig")
     */
    public function edit(Request $request, Category $category)
    {        
        $usersChoice = [];

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $usersChoice = (new CategoryRenderUtils)->getUsersChoice(
                    true, 
                    $this->getUser(), 
                    $this->entityManager->getRepository(User::class)
            );
        }

        $form = $this->createForm(EditType::class, $category, [
            'isGrantedSuperAdmin' => ($this->isGranted('ROLE_SUPER_ADMIN')),
            'usersChoice' => $usersChoice
        ]);
            
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('category');
        }

        return [
            'module_title' => 'Editar categoria',
            'form' => $form->createView()
        ];
    }

    /** 
     * @Route("/admin/category/delete/{id}"), methods=({"DELETE"})
     */
    public function delete(Request $resquest, Category $category)
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('category');
    }
}
