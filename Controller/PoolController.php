<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Pool;
use Abienvenu\KyjoukanBundle\Form\Type\PoolType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/pool/{id}")
 */
class PoolController extends Controller
{
	/**
	 * Displays a form to edit an existing Pool entity.
	 *
	 * @Route("/edit")
	 * @param Request $request
	 * @param Pool $pool
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request, Pool $pool)
	{
		$form = $this->createForm(new PoolType($pool->getPhase()), $pool);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			return $this->redirect($this->generateUrl('abienvenu_kyjoukan_phase_index', [
				                       'slug_event' => $pool->getPhase()->getEvent()->getSlug(), 'slug' => $pool->getPhase()->getSlug()]) . "#pools");
		}

		return $this->render('KyjoukanBundle:Pool:edit.html.twig', ['pool' => $pool, 'form' => $form->createView()]);
	}


	/**
	 * Deletes a Pool entity.
	 *
	 * @Route("/delete")
	 * @param Pool $pool
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction(Pool $pool)
	{
		if ($this->get('kyjoukan.dispatcher')->removePoolFromPhase($pool))
		{
			$this->addFlash('success', "Groupe supprimé");
		}
		else
		{
			$this->addFlash('warning', "Impossible de supprimer le groupe, il y a des matchs déjà joués dedans");
		}

		return $this->redirect($this->generateUrl('abienvenu_kyjoukan_phase_index', [
			                       'slug_event' => $pool->getPhase()->getEvent()->getSlug(),
			                       'slug' => $pool->getPhase()->getSlug()
		                       ]) . "#pools");
	}
}
