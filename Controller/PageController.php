<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
	/**
	 * Dashboard action, main page of the admin
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function dashboardAction() {
		return $this->render('SfsAdminBundle:Core:dashboard.html.twig', array(
		));
	}
}
