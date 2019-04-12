<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class FileMoveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categories = $options['user']->getCategories()->toArray();
        $uncategorized = new Category();
        $uncategorized->setName('Uncategorized');
        $categories = array_merge([$uncategorized], $categories);

        $builder
            ->add('file', HiddenType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choices' => $categories,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'ui fluid dropdown'
                ],
                'empty_data' => [],
                'required' => true,
                'data' => null,
                'choice_value' => function (Category $entity = null) {
                    return $entity ? $entity->getId() : '';
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'user' => null,
        ]);
    }
}
