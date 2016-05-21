<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/pool");
 */
class PoolController extends Controller
{
	/**
	 * Deletes a Pool entity.
	 *
	 * @Route("/delete/{id}")
	 * @param Pool $pool
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction(Pool $pool)
	{
		$em = $this->getDoctrine()->getManager();
		$em->remove($pool);
		$em->flush();

		$this->addFlash("success", "Groupe supprimé");

		return $this->redirect($this->generateUrl('abienvenu_kyjoukan_phase_index', [
			                       'slug_event' => $pool->getPhase()->getEvent()->getSlug(),
			                       'slug' => $pool->getPhase()->getSlug()
		                       ]) . "#pools");
	}
}
