<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
	/**
	 * @Route("/")
	 */
	public function indexAction()
	{
		$events = $this->getDoctrine()->getRepository("KyjoukanBundle:Event")->findBySlug("exemple-de-tournoi");
		return $this->render('KyjoukanBundle:Default:index.html.twig', ['events' => $events]);
	}

	/**
	 * Creates a new Event entity.
	 *
	 * @Route("/new_event")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function newEventAction(Request $request)
	{
		$event = new Event();
		$form = $this->createForm('Abienvenu\KyjoukanBundle\Form\Type\EventType', $event);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->persist($event);
			$em->flush();

			$url = $this->generateUrl('abienvenu_kyjoukan_event_index', ['slug' => $event->getSlug()], true);
			$this->addFlash('danger', "Votre évènement a été créé. Notez bien son URL : <a href='$url'>$url</a>");
			return $this->redirect($url);
		}

		return $this->render('KyjoukanBundle:Default:new_event.html.twig', ['form' => $form->createView()]);
	}

}
