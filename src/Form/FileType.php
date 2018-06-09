<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType as FormFileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fileName', FormFileType::class, [
                'multiple' => true
            ])
            ->add('upload', SubmitType::class, [
                'attr' => [
                    'class' => 'ui button'
                ]
            ])
        ;
    }
}
