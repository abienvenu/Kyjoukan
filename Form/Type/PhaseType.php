<?php

namespace Abienvenu\KyjoukanBundle\Form\Type;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Enum\Rule;
use Abienvenu\KyjoukanBundle\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhaseType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, ['label' => "Nom de la phase"])
			->add('rule', ChoiceType::class, ['choices_as_values' => true, 'choices' => [
				'Poules'=> Rule::ROUNDROBIN,
				'Éliminatoires' => Rule::BRACKETS,
				'Classement Cumulatif' => Rule::CUMULATIVERANK,
			], 'expanded' => true, 'label' => false])
			->add('startDateTime', DateTimeType::class, ['time_widget' => 'text', 'label' => "Heure de début"])
			->add('roundDuration', IntegerType::class, ['label' => "Durée estimée d'un round (en secondes)", 'required' => false])
			->add('teams', EntityType::class, [
				'label' => "Équipes",
				'class' => 'Abienvenu\KyjoukanBundle\Entity\Team',
				'choice_label' => 'name',
				'multiple' => true,
				'expanded' => true,
				'query_builder' => function(TeamRepository $repo) use ($options)
				{
					return $repo->getTeamsForEvent($options['event']);
				}
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => Phase::class, 'event' => null]);
	}
}
