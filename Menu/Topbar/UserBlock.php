<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Menu\Topbar;

class UserBlock extends AbstractTopbarBlock
{
	private $twig;

	public function __construct($twig) {
		$this->twig = $twig;
	}

	public function display() {
		return $this->twig->render('SfsAdminBundle:Menu/TopbarBlocks:user.html.twig', array());
	}
}