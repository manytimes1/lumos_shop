<?php


namespace App\Form;


use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Name'
                ]
            ])
            ->add('model', TextType::class, [
                'attr' => [
                    'placeholder' => 'Model'
                ]
            ])
            ->add('quantity', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Quantity'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description',
                ]
            ])
            ->add('price', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Price',
                ]
            ])
            ->add('isAvailable', ChoiceType::class, [
                'choices' => [
                    'In Stock' => true,
                    'Out of Stock' => false
                ]
            ])
            ->add('image', TextType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }
}