<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/team/{id}")
 */
class TeamController extends Controller
{
	/**
	 * Displays a form to edit an existing Team entity.
	 *
	 * @Route("/edit")
	 */
	public function editAction(Request $request, Team $team)
	{
		$form = $this->createForm('Abienvenu\KyjoukanBundle\Form\Type\TeamType', $team);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			return $this->redirect($this->generateUrl('abienvenu_kyjoukan_event_index', ['slug' => $team->getEvent()->getSlug()]) . "#teams");
		}

		return $this->render('KyjoukanBundle:Team:edit.html.twig', ['team' => $team, 'form' => $form->createView()]);
	}

	/**
	 * Deletes a Team entity.
	 *
	 * @Route("/delete")
	 */
	public function deleteAction(Team $team)
	{
		if ($this->get('kyjoukan.dispatcher')->removeTeamFromEvent($team))
		{
			$this->addFlash('success', "L'équipe a bien été supprimée");
		}
		else
		{
			$this->addFlash('warning', "Impossible de supprimer l'équipe, elle a déjà joué des matchs!");
		}

		return $this->redirect($this->generateUrl('abienvenu_kyjoukan_event_index', ['slug' => $team->getEvent()->getSlug()]) . "#teams");
	}

	/**
	 * Remove a Team from a Phase
	 *
	 * @Route("/remove/{phase}")
	 */
	public function removeFromPhaseAction(Phase $phase, Team $team)
	{
		if ($this->get('kyjoukan.dispatcher')->removeTeamFromPhase($phase, $team))
		{
			$this->addFlash('success', "L'équipe a été supprimée");
		}
		else
		{
			$this->addFlash('warning', "Impossible de supprimer cette équipe, elle a déjà joué des matchs!");
		}
		return $this->redirectToRoute('abienvenu_kyjoukan_phase_index', ['slug_event' => $phase->getEvent()->getSlug(), 'slug' => $phase->getSlug()]);
	}
}
