<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Game;
use Abienvenu\KyjoukanBundle\Form\Type\GameType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/game/{id}")
 */
class GameController extends Controller
{

	/**
	 * Displays a form to edit an existing Team entity.
	 *
	 * @Route("/edit")
	 * @param Request $request
	 * @param Game $game
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request, Game $game)
	{
		$form = $this->createForm(new GameType($game->getPool()), $game);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			return $this->redirect(
				$this->generateUrl('abienvenu_kyjoukan_phase_index', [
					'slug_event' => $game->getPool()->getPhase()->getEvent()->getSlug(),
				    'slug' => $game->getPool()->getPhase()->getSlug(),
				]) . "#games");
		}

		return $this->render('KyjoukanBundle:Game:edit.html.twig', ['game' => $game, 'form' => $form->createView()]);
	}
}
