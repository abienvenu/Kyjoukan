<?php

namespace Abienvenu\KyjoukanBundle\Form\Type;

use Abienvenu\KyjoukanBundle\Entity\Pool;
use Abienvenu\KyjoukanBundle\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PoolType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, ['label' => "Nom"])
			->add('color', TextType::class, ['label' => "Couleur", 'required' => false])
			->add('teams', EntityType::class, [
				'label' => "Équipes",
				'class' => 'Abienvenu\KyjoukanBundle\Entity\Team',
				'choice_label' => 'name',
				'multiple' => true,
				'expanded' => true,
				'query_builder' => function(TeamRepository $repo) use ($options)
				{
					return $repo->getTeamsForPhase($options['phase']);
				}
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => Pool::class, 'phase' => null, 'translation_domain' => false]);
	}
}
