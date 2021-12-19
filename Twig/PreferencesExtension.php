<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class PreferencesExtension extends AbstractExtension implements GlobalsInterface
{
	/**
	 * @var Container
	 */
	private $container;

	/**
	 * @param Container $container
	 */
	public function setContainer(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Generate globals variables accessible from the administration
	 *
	 * @return array
	 */
	public function getGlobals(): array
	{
		$preferences = array(
			'title_text' => $this->container->getParameter('sfs_admin.title_text'),
			'title_logo' => $this->container->getParameter('sfs_admin.title_logo')
		);

		return array('sfs_admin_preferences' => $preferences);
	}

	/**
	 * getName
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'sfs_admin_preferences_twig';
	}
}
