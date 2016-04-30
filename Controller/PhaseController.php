<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
* @Route("/event/{slug_event}/phase/{slug}");
*/

class PhaseController extends Controller
{
	/**
	 * @Route("");
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Phase $phase)
	{
		return $this->render("KyjoukanBundle:Phase:index.html.twig", ['phase' => $phase]);
	}

	/**
	 * @Route("/shuffle")
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function shuffleAction(Phase $phase)
	{
		$this->addFlash('success', "Shuffle not implemented yet.");
		return $this->redirectToRoute("abienvenu_kyjoukan_phase_index", ['slug_event' => $phase->getEvent()->getSlug(), 'slug' => $phase->getSlug()]);
	}

	/**
	 * @Route("/load_teams")
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 *
	 * Copy every team of the Event into the Phase
	 *
	 */
	public function loadTeamsAction(Phase $phase)
	{
		$loaded = 0;
		foreach ($phase->getEvent()->getTeams() as $team)
		{
			if (!$phase->hasTeam($team))
			{
				$phase->addTeam($team);
				$loaded++;
			}
		}
		$this->getDoctrine()->getManager()->flush();

		if ($loaded)
		{
			$this->addFlash('success', "Teams loaded from event: $loaded");
		}
		else
		{
			$this->addFlash('info', "All teams were already loaded");
		}
		return $this->redirectToRoute("abienvenu_kyjoukan_phase_index", ['slug_event' => $phase->getEvent()->getSlug(), 'slug' => $phase->getSlug()]);
	}

	/**
	 * @Route("/dispatch_teams")
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function dispatchTeamsAction(Phase $phase)
	{
		$dispatched = 0;
		foreach ($phase->getTeams() as $team)
		{
			if (!$phase->isTeamPooled($team))
			{
				$pool = $phase->getSmallestPool();
				$pool->addTeam($team);
				$dispatched++;
			}
		}
		$this->getDoctrine()->getManager()->flush();

		if ($dispatched)
		{
			$this->addFlash('success', "Teams dispatched into pools: $dispatched");
		}
		else
		{
			$this->addFlash('info', "All teams were already dispatched");
		}
		return $this->redirectToRoute("abienvenu_kyjoukan_phase_index", ['slug_event' => $phase->getEvent()->getSlug(), 'slug' => $phase->getSlug()]);
	}

	/**
	 * @Route("/gamecards")
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function gameCardsAction(Phase $phase)
	{
		return $this->render("KyjoukanBundle:Phase:gamecards.html.twig", ['phase' => $phase]);
	}
}
