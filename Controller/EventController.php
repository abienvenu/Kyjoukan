<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class EventController extends Controller
{

	/**
	 * @Route("/event/{slug}");
	 * @param Event $event
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Event $event)
	{
		return $this->render("KyjoukanBundle:Event:index.html.twig", ['event' => $event]);
	}
}
