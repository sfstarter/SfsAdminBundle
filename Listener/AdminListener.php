<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Sfs\AdminBundle\Core\CoreAdmin;

class AdminListener
{
	/**
	 * @var CoreAdmin
	 */
	private $core;

	/**
	 * 
	 * @param CoreAdmin $core
	 */
	public function __construct(CoreAdmin $core)
	{
		$this->core = $core;
	}

	/**
	 * onKernelController
	 * 
	 * @param FilterControllerEvent $event
	 */
	public function onKernelController(FilterControllerEvent $event)
	{
        $controller = $event->getController();

        /*
         * $controller peut être une classe ou une closure. Ce n'est pas
         * courant dans Symfony2 mais ça peut arriver.
         * Si c'est une classe, elle est au format array
         */
		if (!is_array($controller)) {
			return;
		}

		if (is_subclass_of($controller[0], 'Sfs\AdminBundle\Controller\AdminController')) {
			$this->core->setCurrentSlug($controller[0]->getSlug());
			$this->core->setCurrentAction();
		}
	}
}
