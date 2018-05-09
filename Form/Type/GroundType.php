<?php

namespace Abienvenu\KyjoukanBundle\Form\Type;


use Abienvenu\KyjoukanBundle\Entity\Ground;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroundType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, ['label' => "Nom du terrain"])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => Ground::class, 'translation_domain' => false]);
	}
}
