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
	 * @Route("/gamecards")
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function gameCardsAction(Phase $phase)
	{
		return $this->render("KyjoukanBundle:Phase:gamecards.html.twig", ['phase' => $phase]);
	}
}
