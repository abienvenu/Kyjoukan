<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Event;
use Abienvenu\KyjoukanBundle\Entity\Phase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PhaseController extends Controller
{
	/**
	 * @Route("/event/{slug_event}/phase/{slug_phase}");
	 * @ParamConverter("event", options={"mapping": {"slug_event": "slug"}})
     * @ParamConverter("phase", options={"mapping": {"slug_phase": "slug"}})
	 * @param Phase $phase
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Event $event, Phase $phase)
	{
		return $this->render("KyjoukanBundle:Phase:index.html.twig", ['phase' => $phase]);
	}
}
