<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;

/**
 * {@inheritDoc}
 */
class SecurityController extends BaseController
{
    /**
     * {@inheritDoc}
     */
	public function renderLogin(array $data)
	{
		$template = sprintf('SfsAdminBundle:Security:login.html.twig');

		return $this->container->get('templating')->renderResponse($template, $data);
	}
}
