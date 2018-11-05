<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Revenue;
use App\Entity\Category;
use App\Utils\RevenueRenderUtils;

class RevenueController extends AbstractController
{
    /**
     * @Route("/admin/revenue", name="revenue")
     */
    public function index()
    {
        return $this->render('admin/revenue/index.html.twig', [
            'controller_name' => 'RevenueController',
            'module_title' => 'Gerenciamento de receitas',
            'userRevenues' => RevenueRenderUtils::getUserRevenues($this->getUser())
        ]);
    }

    /**
     * @Route("/admin/revenue/new", name="revenue_new")
     */
    public function new(Request $request)
    {
        $revenue = new Revenue();

        $form = $this->createFormBuilder($revenue)
                     ->add('name', TextType::class, ['label' => 'Nome', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('description', TextType::class, ['label' => 'Descrição', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('value', MoneyType::class, ['label' => 'Valor', 'currency' => 'BRL', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('date', DateType::class, ['label' => 'Data', 'widget' => 'single_text', 'attr' => ['value' => \date('Y-m-d'), 'class' => 'form-control', 'autocomplete' => false]])
                     ->add('category', EntityType::class, [
                         'label' => 'Categoria',
                         'class' => Category::class,
                         'choices' => $this->getUser()->getCategories(),
                         'attr' => ['class' => 'form-control']
                     ])
                     ->add('save', SubmitType::class, ['label' => 'Adicionar', 'attr' => ['class' => 'btn btn-primary mt-3']])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $revenue = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($revenue);
            $entityManager->flush();

            return $this->redirectToRoute('revenue');
        }

        return $this->render('admin/revenue/new.html.twig', [
            'module_title' => 'Adicionar receita',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/revenue/edit/{id}", name="revenue_edit")
     */
    public function edit(Revenue $revenue, Request $request)
    {
        $form = $this->createFormBuilder($revenue)
                     ->add('name', TextType::class, ['label' => 'Nome', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('description', TextType::class, ['label' => 'Descrição', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('value', MoneyType::class, ['label' => 'Valor', 'currency' => 'BRL', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('date', DateType::class, ['label' => 'Data', 'widget' => 'single_text', 'attr' => ['value' => $revenue->getDate()->format('Y-m-d'), 'class' => 'form-control', 'autocomplete' => false]])
                     ->add('category', EntityType::class, [
                         'label' => 'Categoria',
                         'class' => Category::class,
                         'choices' => $this->getUser()->getCategories(),
                         'attr' => ['class' => 'form-control']
                     ])
                     ->add('save', SubmitType::class, ['label' => 'Salvar', 'attr' => ['class' => 'btn btn-primary mt-3']])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('revenue');
        }

        return $this->render('admin/revenue/edit.html.twig', [
            'module_title' => 'Editar receita',
            'form' => $form->createView()
        ]);
    }

    /** 
     * @Route("/admin/revenue/delete/{id}"), methods=({"DELETE"})
     */
    public function delete(Request $resquest, Revenue $revenue)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($revenue);
        $entityManager->flush();

        return $this->redirectToRoute('revenue');
    }
}
