<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Abienvenu\KyjoukanBundle\Form\PhaseType;

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
	 * Finds and displays a Phase entity.
	 *
	 * @Route("/{id}", name="phase_show")
	 * @Method("GET")
	 */
	public function showAction(Phase $phase)
	{
		$deleteForm = $this->createDeleteForm($phase);

		return $this->render('phase/show.html.twig', array(
			'phase' => $phase,
			'delete_form' => $deleteForm->createView(),
		));
	}

	/**
	 * Displays a form to edit an existing Phase entity.
	 *
	 * @Route("/{id}/edit", name="phase_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editAction(Request $request, Phase $phase)
	{
		$deleteForm = $this->createDeleteForm($phase);
		$editForm = $this->createForm('Abienvenu\KyjoukanBundle\Form\PhaseType', $phase);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($phase);
			$em->flush();

			return $this->redirectToRoute('phase_edit', array('id' => $phase->getId()));
		}

		return $this->render('phase/edit.html.twig', array(
			'phase' => $phase,
			'edit_form' => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
		));
	}

	/**
	 * Deletes a Phase entity.
	 *
	 * @Route("/{id}", name="phase_delete")
	 * @Method("DELETE")
	 */
	public function deleteAction(Request $request, Phase $phase)
	{
		$form = $this->createDeleteForm($phase);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($phase);
			$em->flush();
		}

		return $this->redirectToRoute('phase_index');
	}

	/**
	 * Creates a form to delete a Phase entity.
	 *
	 * @param Phase $phase The Phase entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(Phase $phase)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('phase_delete', array('id' => $phase->getId())))
			->setMethod('DELETE')
			->getForm()
		;
	}
}
