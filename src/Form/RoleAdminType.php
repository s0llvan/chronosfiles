<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Role;

class RoleAdminType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class)
			->add('slug', TextType::class)
			->add('uploadFileSizeLimit')
			->add('storageSizeLimit')
			->add('save', SubmitType::class, [
				'attr' => [
					'class' => 'ui button primary'
				]
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Role::class,
		]);
	}
}
