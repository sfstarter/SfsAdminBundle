<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Menu\Topbar;

abstract class TopbarBlockAbstract implements TopbarBlockInterface
{
	protected $twig;

	protected $attributes = array();

	public function __construct($twig) {
		$this->twig = $twig;

		$this->setAttributes();
	}

	public function setAttributes() {
	}

	/**
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	abstract public function display();
}
