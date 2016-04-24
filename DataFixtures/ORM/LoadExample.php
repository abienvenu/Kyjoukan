<?php
namespace Abienvenu\KyjoukanBundle\DataFixtures\ORM;


use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Pool;
use Abienvenu\KyjoukanBundle\Enum\Rule;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Abienvenu\KyjoukanBundle\Entity\Team;
use Abienvenu\KyjoukanBundle\Entity\Event;
use Abienvenu\KyjoukanBundle\Entity\Ground;

class LoadTeam implements FixtureInterface
{
	public function load(ObjectManager $manager)
	{
		// Event
		$event = new Event();
		$event->setName("Tournoi Volley Chanteloup du 11 juin");
		$manager->persist($event);

		// Teams
		$teams = [
			"Les incorruptibles",
			"Boit-sans-soif",
			"Les intouchables",
			"Oclomosimanapa",
			"Chauds les marrons, chaud!",
			"Nevguen",
			"Chanteloup Badminton",
			"C'est sur la ligne",
			"972",
			"Pathinacé",
			"ONSPJ",
			"La prévière",
			"Kikonféla",
			"ZEbet",
		];

		foreach ($teams as $teamName)
		{
			$team = new Team();
			$team->setEvent($event);
			$team->setName($teamName);
			$manager->persist($team);
		}

		// Grounds
		$grounds = [
			"A",
			"B",
			"C",
		];

		foreach ($grounds as $groundName)
		{
			$ground = new Ground();
			$ground->setEvent($event);
			$ground->setName($groundName);
			$manager->persist($ground);
		}

		// Phase
		$phase = new Phase();
		$phase->setEvent($event);
		$phase->setName("Poules phase 1");
		$phase->setRule(Rule::ROUNDROBIN);
		$manager->persist($phase);

		// Pools
		for ($i = 1; $i <= 3; $i++)
		{
			$pool = new Pool();
			$pool->setPhase($phase);
			$manager->persist($pool);
		}

		$manager->flush();
	}
}
