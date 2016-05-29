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
		$event->setName("Tournoi Volley Chanteloup du 10 juin");
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
			"Gibon",
			"Sardines",
			"Les cressonnettes",
			"Loulou coptères",
		];

		foreach ($teams as $teamName)
		{
			$team = new Team();
			$team->setName($teamName);
			$event->addTeam($team);
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
			$ground->setName($groundName);
			$event->addGround($ground);
		}

		// Phase
		$phase = new Phase();
		$phase->setName("Poules phase 1");
		$phase->setRule(Rule::ROUNDROBIN);
		$phase->setStartDateTime(new \DateTime());
		$phase->setRoundDuration(12*60);
		$event->addPhase($phase);

		// Pools
		$colors = [1 => "green", "red", "blue", "darkorange"];
		for ($i = 1; $i <= 4; $i++)
		{
			$pool = new Pool();
			$pool->setName("Pool $i");
			$pool->setColor($colors[$i]);
			$phase->addPool($pool);
		}

		$manager->flush();
	}
}
