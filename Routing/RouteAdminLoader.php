<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use Sfs\AdminBundle\Core\CoreAdmin;

class RouteAdminLoader extends Loader
{
	/**
	 * @var CoreAdmin
	 */
	protected $core;

	/**
	 * @var bool
	 */
    private $loaded = false;

    /**
     * 
     * @param CoreAdmin $core
     */
    public function __construct(CoreAdmin $core)
    {
    	$this->core = $core;
    }

    /**
     * load
     * 
     * @param unknown $resource
     * @param mixed $type
     * 
     * @return array
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the admin routes loader twice');
        }

		$routes = new RouteCollection();
		$adminRoutes = $this->core->getRoutes();
		$adminResources = $this->core->getAdmins();

		// Foreach admin resource, and each generic page it requires: loop and create a route
		foreach($adminRoutes as $slug => $adminRoute) {
			foreach($adminRoute as $action) {
				// prepare a new route
				$path = $action['path'];
		        $defaults = array(
		            '_controller' => $adminResources[$slug]['service'] .':'. $action['action'],
		        );
				$route = new Route($path, $defaults, $action['requirements']);
		        $routeName = $action['route'];

		        // add the new route to the route collection
		        $routes->add($routeName, $route);
			}
		}

		$this->loaded = true;

		return $routes;
	}

	/**
	 * supports
	 * 
	 * @param unknown $resource
	 * @param mixed $type
	 */
	public function supports($resource, $type = null)
	{
		return 'adminRoute' === $type;
	}
}
