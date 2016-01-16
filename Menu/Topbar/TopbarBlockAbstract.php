<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Menu\Topbar;

abstract class TopbarBlockAbstract implements TopbarBlockInterface
{
	private $twig;

	public function __construct($twig) {
		$this->twig = $twig;
	}

	abstract public function display();
}
