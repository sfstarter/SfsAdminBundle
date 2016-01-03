<?php 

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

class PreferencesExtension extends \Twig_Extension
{
	private $container;

	public function __construct(Container $container) {
		$this->container = $container;
	}

	public function getGlobals() {
		$preferences = array(
			'title_text' => $this->container->getParameter('sfs_admin.title_text'),
			'title_logo' => $this->container->getParameter('sfs_admin.title_logo')
		);

		return array('sfs_admin_preferences' => $preferences);
	}

    public function getName()
    {
        return 'sfs_admin_preferences_twig';
    }
}
