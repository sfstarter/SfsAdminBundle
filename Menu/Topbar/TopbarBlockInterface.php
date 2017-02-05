<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Menu\Topbar;

interface TopbarBlockInterface
{
	public function setAttributes();
	public function getAttributes();
	public function display();
}
