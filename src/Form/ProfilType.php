<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('username', TextType::class, [
            'disabled' => true,
            'constraints' => [
                new Length([
                    'min' => 3,
                    'minMessage' => 'Password is too short. It should have {{ limit }} characters or more.',
                    'max' => 12,
                    'maxMessage' => 'Password is too long. It should have {{ limit }} characters or less.'
                ])
            ]
        ])
        ->add('email', EmailType::class, [
            'required' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Email is required'
                ]),
                new Email([
                    'message' => 'Email is not valid'
                ])
            ]
        ])
        ->add('save', SubmitType::class, [
            'attr' => [
                'class' => 'ui button'
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
