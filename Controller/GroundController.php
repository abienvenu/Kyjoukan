<?php

namespace Abienvenu\KyjoukanBundle\Controller;

use Abienvenu\KyjoukanBundle\Entity\Ground;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/event/{slug_event}/ground/{id}")
 */
class GroundController extends Controller
{
	/**
	 * Displays a form to edit an existing Ground entity.
	 *
	 * @Route("/edit")
	 * @param Request $request
	 * @param Ground $ground
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request, Ground $ground)
	{
		$form = $this->createForm('Abienvenu\KyjoukanBundle\Form\Type\GroundType', $ground);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			return $this->redirect($this->generateUrl('abienvenu_kyjoukan_event_index', ['slug' => $ground->getEvent()->getSlug()]) . "#grounds");
		}

		return $this->render('KyjoukanBundle:Ground:edit.html.twig', ['ground' => $ground, 'form' => $form->createView()]);
	}

	/**
	 * Deletes a Ground entity.
	 *
	 * @Route("/delete")
	 * @param Ground $ground
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction(Ground $ground)
	{
		$em = $this->getDoctrine()->getManager();
		$em->remove($ground);
		$em->flush();

		return $this->redirect($this->generateUrl('abienvenu_kyjoukan_event_index', ['slug' => $ground->getEvent()->getSlug()]) . "#grounds");
	}
}
