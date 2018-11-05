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
use App\Entity\Expense;
use App\Entity\Revenue;
use App\Entity\Category;
use App\Utils\ExpenseRenderUtils;

class ExpenseController extends AbstractController
{
    /**
     * @Route("/admin/expense", name="expense")
     */
    public function index()
    {
        return $this->render('admin/expense/index.html.twig', [
            'controller_name' => 'ExpenseController',
            'module_title' => 'Gerenciamento de despesas',
            'userExpenses' => ExpenseRenderUtils::getUserExpenses($this->getUser())
        ]);
    }

    /**
     * @Route("/admin/expense/new", name="expense_new")
     */
    public function new(Request $request)
    {
        $expense = new Expense();

        $form = $this->createFormBuilder($expense)
                     ->add('name', TextType::class, ['label' => 'Nome', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('description', TextType::class, ['label' => 'Descrição', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('place', TextType::class, ['label' => 'Local', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
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
            $expense = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($expense);
            $entityManager->flush();

            return $this->redirectToRoute('expense');
        }

        return $this->render('admin/expense/new.html.twig', [
            'module_title' => 'Adicionar despesa',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/expense/edit/{id}", name="expense_edit")
     */
    public function edit(Expense $expense, Request $request)
    {
        $form = $this->createFormBuilder($expense)
                     ->add('name', TextType::class, ['label' => 'Nome', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('description', TextType::class, ['label' => 'Descrição', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('place', TextType::class, ['label' => 'Nome', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('value', MoneyType::class, ['label' => 'Valor', 'currency' => 'BRL', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
                     ->add('date', DateType::class, ['label' => 'Data', 'widget' => 'single_text', 'attr' => ['value' => \date('Y-m-d'), 'class' => 'form-control', 'autocomplete' => false]])
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

            return $this->redirectToRoute('expense');
        }

        return $this->render('admin/expense/edit.html.twig', [
            'module_title' => 'Editar despesa',
            'form' => $form->createView()
        ]);
    }

    /** 
     * @Route("/admin/expense/delete/{id}"), methods=({"DELETE"})
     */
    public function delete(Request $resquest, Expense $expense)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($expense);
        $entityManager->flush();

        return $this->redirectToRoute('expense');
    }
}
