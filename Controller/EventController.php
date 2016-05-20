<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Event;
use Abienvenu\KyjoukanBundle\Entity\Phase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/event/{slug}");
 */
class EventController extends Controller
{

	/**
	 * @Route("");
	 * @param Event $event
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Event $event)
	{
		return $this->render("KyjoukanBundle:Event:index.html.twig", ['event' => $event]);
	}

	/**
	 * Creates a new Phase entity.
	 *
	 * @Route("/new_phase")
	 * @param Request $request
	 * @param Event $event
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function newPhaseAction(Request $request, Event $event)
	{
		$phase = new Phase();
		$phase->setStartDateTime(new \DateTime());
		$form = $this->createForm('Abienvenu\KyjoukanBundle\Form\PhaseType', $phase);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$event->addPhase($phase);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			return $this->redirectToRoute('abienvenu_kyjoukan_event_index', ['slug' => $event->getSlug()]);
		}

		return $this->render('KyjoukanBundle:Event:new_phase.html.twig', ['event' => $event, 'phase' => $phase, 'form' => $form->createView()]);
	}
}
