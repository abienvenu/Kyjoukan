<?php

namespace Abienvenu\KyjoukanBundle\Form\Type;

use Abienvenu\KyjoukanBundle\Entity\Event;
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
	private $event;

	public function __construct(Event $event)
	{
		$this->event = $event;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, ['label' => "Nom de la phase"])
			->add('rule', ChoiceType::class, ['choices_as_values' => true, 'choices' => [
				'Poules'=> Rule::ROUNDROBIN,
				'Éliminatoires' => Rule::BRACKETS,
			], 'expanded' => true, 'label' => false])
			->add('startDateTime', DateTimeType::class, ['time_widget' => 'text', 'label' => "Heure de début"])
			->add('roundDuration', IntegerType::class, ['label' => "Durée estimée d'un round (en secondes)", 'required' => false])
			->add('teams', EntityType::class, [
				'label' => "Équipes",
				'class' => 'Abienvenu\KyjoukanBundle\Entity\Team',
				'property' => 'name',
				'multiple' => true,
				'expanded' => true,
				'query_builder' => function(TeamRepository $repo)
				{
					return $repo->getTeamsForEvent($this->event);
				}
			])
		;
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => 'Abienvenu\KyjoukanBundle\Entity\Phase']);
	}
}
