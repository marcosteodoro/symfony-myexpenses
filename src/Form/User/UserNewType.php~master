<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->add('save', SubmitType::class, ['label' => 'Adicionar', 'attr' => ['class' => 'btn btn-primary mt-3']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
