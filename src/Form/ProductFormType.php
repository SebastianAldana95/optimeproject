<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->where('c.active = 1')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name'
            ])
            ->add('code', TextType::class, [ 'attr' => [
                'minlength' => 4,
                'maxlength' => 10,
                'alphanumeric' => true],
                'required' => true
            ])
            ->add('name', TextType::class, [ 'attr' => [
                'minlength' => 4],
                'required' => true
            ])
            ->add('description', TextType::class, [
                'required' => true
            ])
            ->add('brand', TextType::class, [
                'required' => true
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'COP',
                'scale' => 3,
                'required' => true
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
