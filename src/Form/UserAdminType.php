<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Role;

class UserAdminType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('username', TextType::class)
			->add('email', EmailType::class);
		if($options['super_admin']) {
			$builder->add('role', EntityType::class, [
				'class' => Role::class,
				'choice_label' => 'name'
			]);
		}
		$builder->add('save', SubmitType::class, [
			'attr' => [
				'class' => 'ui button primary'
			]
		]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
			'super_admin' => false
		]);
	}
}
