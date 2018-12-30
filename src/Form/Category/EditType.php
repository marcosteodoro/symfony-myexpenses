<?php

namespace App\Form\Category;

use App\Entity\Category;
use App\Entity\User;
use App\Utils\CategoryRenderUtils;
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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class EditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nome', 'attr' => ['class' => 'form-control', 'autocomplete' => false]])
            ->add('description', TextType::class, ['label' => 'Descrição', 'attr' => ['class' => 'form-control', 'autocomplete' => false]]);

        if ($options['isGrantedSuperAdmin']) {
            $builder
                ->add('user', EntityType::class, [
                    'label' => 'Usuário',
                    'class' => User::class,
                    'choices' => $options['usersChoice'],
                    'attr' => ['class' => 'form-control']
            ]);
        }

        $builder
            ->add('save', SubmitType::class, ['label' => 'Salvar', 'attr' => ['class' => 'btn btn-primary mt-3']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);

        $resolver->setRequired([
            'isGrantedSuperAdmin',
            'usersChoice'
        ]);
    }
}
