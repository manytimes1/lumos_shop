<?php


namespace App\Form;


use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label' => false])
            ->add('lastName', TextType::class, ['label' => false])
            ->add('phone', TextType::class, ['label' => false])
            ->add('location', TextType::class, ['label' => false])
            ->add('city', TextType::class, ['label' => false])
            ->add('state', TextType::class, ['label' => false])
            ->add('zipCode', NumberType::class, ['label' => false])
            ->add('picture', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class
        ]);
    }
}