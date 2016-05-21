<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/event/{slug_event}/team/{id}");
 */
class TeamController extends Controller
{
	/**
	 * Displays a form to edit an existing Team entity.
	 *
	 * @Route("/edit")
	 * @param Request $request
	 * @param Team $team
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
	 * @param Team $team
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction(Team $team)
	{
		$em = $this->getDoctrine()->getManager();
		$em->remove($team);
		$em->flush();

		return $this->redirect($this->generateUrl('abienvenu_kyjoukan_event_index', ['slug' => $team->getEvent()->getSlug()]) . "#teams");
	}
}
