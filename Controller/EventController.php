<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Event;
use Abienvenu\KyjoukanBundle\Entity\Ground;
use Abienvenu\KyjoukanBundle\Entity\Phase;
use Abienvenu\KyjoukanBundle\Entity\Team;
use Abienvenu\KyjoukanBundle\Form\Type\PhaseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/event/{slug}")
 */
class EventController extends Controller
{

	/**
	 * @Route("")
	 */
	public function indexAction(Event $event)
	{
		if (!count($event->getPhases()))
		{
			$this->addFlash('warning', "Veuillez créer au moins une phase");
		}
		if (!count($event->getTeams()))
		{
			$this->addFlash('warning', "Veuillez créer au moins une équipe");
		}
		if (!count($event->getGrounds()))
		{
			$this->addFlash('warning', "Veuillez créer au moins un terrain");
		}

		return $this->render("KyjoukanBundle:Event:index.html.twig", ['event' => $event]);
	}

	/**
	 * Displays a form to edit an existing Event entity.
	 *
	 * @Route("/edit")
	 */
	public function editAction(Request $request, Event $event)
	{
		$oldSlug = $event->getSlug();

		$form = $this->createForm('Abienvenu\KyjoukanBundle\Form\Type\EventType', $event);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			if ($event->getSlug() != $oldSlug)
			{
				$url = $this->generateUrl("abienvenu_kyjoukan_event_index", ['slug' => $event->getSlug()], true);
				$this->addFlash('danger', "L'adresse de votre évènement a été modifiée. Notez bien sa nouvelle URL : <a href='$url'>$url</a>");
			}
			return $this->redirectToRoute("abienvenu_kyjoukan_event_index", ['slug' => $event->getSlug()]);
		}

		return $this->render('KyjoukanBundle:Event:edit.html.twig', ['event' => $event, 'form' => $form->createView()]);
	}

	/**
	 * Deletes a Event entity.
	 *
	 * @Route("/delete")
	 */
	public function deleteAction(Event $event)
	{
		$em = $this->getDoctrine()->getManager();
		$em->remove($event);
		$em->flush();

		return $this->redirectToRoute('abienvenu_kyjoukan_default_index');
	}

	/**
	 * Creates a new Phase entity.
	 *
	 * @Route("/new_phase")
	 */
	public function newPhaseAction(Request $request, Event $event)
	{
		$phase = new Phase();
		$phase->setStartDateTime(new \DateTime());
		$form = $this->createForm(PhaseType::class, $phase, ['event' => $event]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$event->addPhase($phase);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			return $this->redirectToRoute('abienvenu_kyjoukan_event_index', ['slug' => $event->getSlug()]);
		}

		return $this->render('KyjoukanBundle:Event:new_phase.html.twig', ['event' => $event, 'form' => $form->createView()]);
	}

	/**
	 * Create a new Team entity
	 *
	 * @Route("/new_team")
	 */
	public function newTeamAction(Request $request, Event $event)
	{
		$team = new Team();
		$form = $this->createForm('Abienvenu\KyjoukanBundle\Form\Type\TeamType', $team);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$event->addTeam($team);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			return $this->redirect($this->generateUrl('abienvenu_kyjoukan_event_index', ['slug' => $event->getSlug()]) . "#teams");
		}
		return $this->render('KyjoukanBundle:Event:new_team.html.twig', ['event' => $event, 'form' => $form->createView()]);
	}

	/**
	 * Create a new Ground entity
	 *
	 * @Route("/new_ground")
	 */
	public function newGroundAction(Request $request, Event $event)
	{
		$ground = new Ground();
		$form = $this->createForm('Abienvenu\KyjoukanBundle\Form\Type\GroundType', $ground);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$event->addGround($ground);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			return $this->redirect($this->generateUrl('abienvenu_kyjoukan_event_index', ['slug' => $event->getSlug()]) . "#grounds");
		}

		return $this->render('KyjoukanBundle:Event:new_ground.html.twig', ['event' => $event, 'form' => $form->createView()]);
	}
}
