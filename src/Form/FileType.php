<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType as FormFileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;

class FileType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$categories = $options['user']->getCategories();

		$builder
			->add('fileName', FormFileType::class, [
				'multiple' => true
			])
			->add('category', EntityType::class, [
				'class' => Category::class,
				'choices' => $categories,
				'choice_label' => 'name',
				'attr' => [
					'class' => 'ui fluid dropdown'
				],
				'empty_data' => '',
				'required' => false,
				'choice_value' => null
			])
			->add('upload', SubmitType::class, [
				'attr' => [
					'class' => 'ui button'
				]
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'user' => null,
		]);
	}
}
