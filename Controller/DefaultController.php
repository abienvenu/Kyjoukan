<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
	/**
	 * @Route("/")
	 */
	public function indexAction()
	{
		$events = $this->getDoctrine()->getRepository("KyjoukanBundle:Event")->findAll();
		return $this->render('KyjoukanBundle:Default:index.html.twig', ["events" => $events]);
	}
}
