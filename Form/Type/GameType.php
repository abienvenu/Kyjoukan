<?php

namespace Abienvenu\KyjoukanBundle\Form\Type;

use Abienvenu\KyjoukanBundle\Entity\Game;
use Abienvenu\KyjoukanBundle\Repository\GroundRepository;
use Abienvenu\KyjoukanBundle\Repository\RoundRepository;
use Abienvenu\KyjoukanBundle\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('team1', EntityType::class, [
				'class' => "KyjoukanBundle:Team",
				'choice_label' => 'name',
				'label' => "Équipe A",
				'query_builder' => function(TeamRepository $repo) use ($options)
				{
					return $repo->getTeamsForPool($options['pool']);
				}
			])
			->add('team2', EntityType::class, [
				'class' => "KyjoukanBundle:Team",
				'choice_label' => 'name',
				'label' => "Équipe B",
				'query_builder' => function(TeamRepository $repo) use ($options)
				{
					return $repo->getTeamsForPool($options['pool']);
				}
			])
			->add('referee', EntityType::class, [
				'class' => "KyjoukanBundle:Team",
				'choice_label' => 'name',
				'label' => "Arbitre",
				'query_builder' => function(TeamRepository $repo) use ($options)
				{
					return $repo->getTeamsForPhase($options['pool']->getPhase());
				}
			])
			->add('ground', EntityType::class, [
				'class' => "KyjoukanBundle:Ground",
				'choice_label' => 'name',
				'label' => "Terrain",
			    'query_builder' => function(GroundRepository $repo) use ($options)
					{
						return $repo->getGroundsForEvent($options['pool']->getPhase()->getEvent());
					}
			])
			->add('round', EntityType::class, [
				'class' => "KyjoukanBundle:Round",
				'choice_label' => 'number',
				'label' => "Tour",
			    'query_builder' => function(RoundRepository $repo) use ($options)
					{
						return $repo->getRoundsForPhase($options['pool']->getPhase());
					}
			])
			->add('score1', IntegerType::class, ['label' => "Score de A", 'required' => false])
			->add('score2', IntegerType::class, ['label' => "Score de B", 'required' => false])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => Game::class, 'pool' => null, 'translation_domain' => false]);
	}
}
