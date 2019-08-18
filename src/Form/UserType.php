<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['label' => false])
            ->add('username', TextType::class, ['label' => false])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'first_options' => ['label' => false],
                'second_options' => ['label' => false],
            ])
            ->add('profile', ProfileType::class)
            ->add('roles', EntityType::class, [
                'label' => false,
                'class' => Role::class,
                'mapped' => false,
                'query_builder' => function (EntityRepository $e) {
                    return $e->createQueryBuilder('r')
                        ->where('r.name = :name')
                        ->setParameter('name', 'ROLE_EDITOR');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
