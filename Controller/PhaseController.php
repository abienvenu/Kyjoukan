<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Form\Type\PhaseType;
use Abienvenu\KyjoukanBundle\Service\CheckService;
use Abienvenu\KyjoukanBundle\Service\DispatcherService;
use Abienvenu\KyjoukanBundle\Service\RankService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/event/{slug_event}/phase/{slug}")
 */
class PhaseController extends Controller
{
	/**
	 * @Route("")
	 */
	public function indexAction(CheckService $checkService, Phase $phase)
	{
		$errors = [
			'team' => $checkService->checkPhaseTeams($phase),
			'pool' => $checkService->checkPhasePools($phase),
		    'game' => $checkService->checkPhaseGames($phase),
		];
		return $this->render("KyjoukanBundle:Phase:index.html.twig", ['phase' => $phase, 'errors' => $errors]);
	}

	/**
	 * Put every team of the Event into the Phase
	 * The user may remove some of them (in case they are unable to participate in the given phase)
	 *
	 * @Route("/load_teams")
	 */
	public function loadTeamsAction(DispatcherService $dispatcherService, Phase $phase)
	{
		$loaded = $dispatcherService->loadTeamsIntoPhase($phase);
		if ($loaded)
		{
			$this->addFlash('success', "Équipes chargées avec succès : $loaded");
		}
		else
		{
			$this->addFlash('info', "Toutes les équipes étaient déjà chargées");
		}
		return $this->redirectToRoute("abienvenu_kyjoukan_phase_index", ['slug_event' => $phase->getEvent()->getSlug(), 'slug' => $phase->getSlug()]);
	}

	/**
	 * @Route("/dispatch_teams")
	 */
	public function dispatchTeamsAction(DispatcherService $dispatcherService, Phase $phase)
	{
		$dispatched = $dispatcherService->dispatchTeamsIntoPools($phase);
		if ($dispatched)
		{
			$this->addFlash('success', "Équipes réparties dans des groupes : $dispatched");
		}
		else
		{
			$this->addFlash('info', "Toute les équipes étaient déjà réparties");
		}
		return $this->redirect(
			$this->generateUrl("abienvenu_kyjoukan_phase_index", ['slug_event' => $phase->getEvent()->getSlug(), 'slug' => $phase->getSlug()]) . "#pools");
	}

	/**
	 * Clean all unplayed games
	 *
	 * @Route("/clean")
	 */
	public function cleanAction(DispatcherService $dispatcherService, Phase $phase)
	{
		$dispatcherService->cleanGames($phase);
		$this->addFlash('success', "Nettoyage effectué.");
		return $this->redirect(
			$this->generateUrl("abienvenu_kyjoukan_phase_index", ['slug_event' => $phase->getEvent()->getSlug(), 'slug' => $phase->getSlug()]) . "#games");
	}

	/**
	 * Shuffle games into the phase
	 *
	 * @Route("/shuffle")
	 */
	public function shuffleAction(DispatcherService $dispatcherService, Phase $phase)
	{
		if (!count($phase->getEvent()->getGrounds()))
		{
			$this->addFlash('danger', "Impossible de programmer, veuillez d'abord ajouter des terrains!");
		}
		else if (!count($phase->getPools()))
		{
			$this->addFlash('danger', "Impossible de programmer, veuillez d'abord ajouter des groupes!");
		}
		else
		{
			$dispatcherService->shuffleGames($phase);
			$this->addFlash('success', "Les matchs sont programmés.");
		}
		return $this->redirect(
			$this->generateUrl("abienvenu_kyjoukan_phase_index", ['slug_event' => $phase->getEvent()->getSlug(), 'slug' => $phase->getSlug()]) . "#games");
	}

	/**
	 * @Route("/gamecards/{page}", requirements={"page": "\d+"})
	 */
	public function gameCardsAction(Phase $phase, int $page)
	{
		$cardsPerPage = 6;
		$games = $this->getDoctrine()->getRepository('KyjoukanBundle:Game')->findByPhase($phase);
		$pages = floor((count($games) - 1) / 6) + 1;
		$games = array_slice($this->getDoctrine()->getRepository('KyjoukanBundle:Game')->findByPhase($phase), ($page-1)*$cardsPerPage, $cardsPerPage);
		return $this->render("KyjoukanBundle:Phase:gamecards.html.twig", ['phase' => $phase, 'games' => $games, 'page' => $page, 'pages' => $pages]);
	}

	/**
	 * @Route("/planning")
	 */
	public function planningAction(Phase $phase)
	{
		return $this->render("KyjoukanBundle:Phase:planning.html.twig", ['phase' => $phase]);
	}

	/**
	 * @Route("/ranking")
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function rankingAction(RankService $rankService, Phase $phase)
	{
		$rankings = [];
		foreach ($phase->getPools() as $pool)
		{
			$index = 0;
			$key = $pool->getName();
			while (array_key_exists($key, $rankings))
			{
				$index++;
				$key = "{$pool->getName()}_$index";
			}
			$rankings[$key] = $rankService->getPoolRanks($pool);
		}
		return $this->render("KyjoukanBundle:Phase:ranking.html.twig", ['rankings' => $rankings]);
	}

	/**
	 * Displays a form to edit an existing Phase entity.
	 *
	 * @Route("/edit")
	 */
	public function editAction(Request $request, Phase $phase)
	{
		$form = $this->createForm(PhaseType::class, $phase, ['event' => $phase->getEvent()]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			return $this->redirectToRoute('abienvenu_kyjoukan_event_index', ['slug' => $phase->getEvent()->getSlug()]);
		}

		return $this->render('KyjoukanBundle:Phase:edit.html.twig', ['phase' => $phase, 'form' => $form->createView()]);
	}

	/**
	 * Deletes a Phase entity.
	 *
	 * @Route("/delete")
	 */
	public function deleteAction(Phase $phase)
	{
		$em = $this->getDoctrine()->getManager();
		// It is important to remove the teams, or SQLite will create a constraint violation if we recreate another phase with the same teams
		foreach ($phase->getTeams() as $team)
		{
			$phase->removeTeam($team);
		}
		$em->flush();
		$em->remove($phase);
		$em->flush();

		return $this->redirectToRoute('abienvenu_kyjoukan_event_index', ['slug' => $phase->getEvent()->getSlug()]);
	}
}
