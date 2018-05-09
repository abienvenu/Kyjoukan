<?php

namespace Abienvenu\KyjoukanBundle\Form\Type;

use Abienvenu\KyjoukanBundle\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, ['label' => "Nom de l'évènement"])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => Event::class, 'translation_domain' => false]);
	}
}
