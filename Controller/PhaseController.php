<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
	 * Put every team of the Event into the Phase
	 * The user may remove some of them (in case they are unable to participate in the given phase)
	 *
	 * @Route("/load_teams")
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function loadTeamsAction(Phase $phase)
	{
		$loaded = $this->get('kyjoukan.dispatcher')->loadTeamsIntoPhase($phase);
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
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function dispatchTeamsAction(Phase $phase)
	{
		$dispatched = $this->get('kyjoukan.dispatcher')->dispatchTeamsIntoPools($phase);
		if ($dispatched)
		{
			$this->addFlash('success', "Équipes réparties dans des groupes : $dispatched");
		}
		else
		{
			$this->addFlash('info', "Toute les équipes étaient déjà réparties");
		}
		return $this->redirectToRoute("abienvenu_kyjoukan_phase_index", ['slug_event' => $phase->getEvent()->getSlug(), 'slug' => $phase->getSlug()]);
	}

	/**
	 * Shuffle games into the phase
	 *
	 * @Route("/shuffle")
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function shuffleAction(Phase $phase)
	{
		$this->get('kyjoukan.dispatcher')->shuffleGames($phase);
		$this->addFlash('success', "Les matchs sont programmés.");
		return $this->redirectToRoute("abienvenu_kyjoukan_phase_index", ['slug_event' => $phase->getEvent()->getSlug(), 'slug' => $phase->getSlug()]);
	}

	/**
	 * @Route("/gamecards/{page}", requirements={"page": "\d+"})
	 * @param Phase $phase
	 * @param int $page
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function gameCardsAction(Phase $phase, $page)
	{
		$cardsPerPage = 6;
		$games = $this->getDoctrine()->getRepository('KyjoukanBundle:Game')->findByPhase($phase);
		$pages = floor((count($games) - 1) / 6) + 1;
		$games = array_slice($this->getDoctrine()->getRepository('KyjoukanBundle:Game')->findByPhase($phase), ($page-1)*$cardsPerPage, $cardsPerPage);
		return $this->render("KyjoukanBundle:Phase:gamecards.html.twig", ['phase' => $phase, 'games' => $games, 'page' => $page, 'pages' => $pages]);
	}

	/**
	 * @Route("/planning")
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function planningAction(Phase $phase)
	{
		return $this->render("KyjoukanBundle:Phase:planning.html.twig", ['phase' => $phase]);
	}

	/**
	 * Displays a form to edit an existing Phase entity.
	 *
	 * @Route("/edit")
	 * @param Request $request
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request, Phase $phase)
	{
		$form = $this->createForm('Abienvenu\KyjoukanBundle\Form\Type\PhaseType', $phase);
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
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction(Phase $phase)
	{
		$em = $this->getDoctrine()->getManager();
		$em->remove($phase);
		$em->flush();

		return $this->redirectToRoute('abienvenu_kyjoukan_event_index', ['slug' => $phase->getEvent()->getSlug()]);
	}


	/**
	 * Remove a Team from a Phase
	 *
	 * @Route("/remove_team/{team}")
	 * @param Phase $phase
	 * @param Team $team
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function removeTeamAction(Phase $phase, Team $team)
	{
		$phase->removeTeam($team);
		$em = $this->getDoctrine()->getManager();
		$em->flush();

		$this->addFlash('success', "Une équipe supprimée");
		return $this->redirectToRoute('abienvenu_kyjoukan_phase_index', ['slug_event' => $phase->getEvent()->getSlug(), 'slug' => $phase->getSlug()]);
	}
}
