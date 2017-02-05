<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Menu\Topbar;

class UserBlock extends TopbarBlockAbstract
{
	public function setAttributes() {
		$this->attributes = array(
			'class' => 'dropdown navbar-profile',
		);
	}

	public function display() {
		return $this->twig->render('SfsAdminBundle:Menu/TopbarBlocks:user.html.twig', array());
	}
}
