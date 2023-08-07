<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',    TextType::class)
            ->add('category', EntityType::class, [
                // looks for choices from this entity
                'class' => Category::class,

                'placeholder' => 'Choisissez la catégorie',
            
                // uses the User.username property as the visible option string
                'choice_label' => 'name',
            ])
            ->add('image')
            ->add('content')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
