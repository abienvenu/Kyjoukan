<?php

namespace Abienvenu\KyjoukanBundle\Form\Type;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PoolType extends AbstractType
{
	private $phase;

	public function __construct(Phase $phase)
	{
		$this->phase = $phase;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, ['label' => "Nom", 'required' => false])
			->add('color', TextType::class, ['label' => "Couleur", 'required' => false])
			->add('teams', EntityType::class, [
				'label' => "Équipes",
				'class' => 'Abienvenu\KyjoukanBundle\Entity\Team',
				'property' => 'name',
				'multiple' => true,
				'expanded' => true,
				'query_builder' => function(TeamRepository $repo)
				{
					return $repo->getTeamsForPhase($this->phase);
				}
			])
		;
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => 'Abienvenu\KyjoukanBundle\Entity\Pool']);
	}
}
