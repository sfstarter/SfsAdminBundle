<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Core;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\Common\Util\ClassUtils;

class CoreAdmin extends ContainerAware
{
	// Array of every admin resources available (should look like array('slug' => array(service, entityClass), 'slug' => array(service, entityClass), ...)
	protected $admins = array();
	// Every damn routes (should look like array('slug' => array('action' => array('route', 'action', 'path', 'requirements'))) )
	protected $routes = array();

	// Slug of the admin resource
	private $currentSlug = null;
	// Action is list, create ...
	private $currentAction = null;

	public function getCurrentSlug() {
		return $this->currentSlug;
	}
	public function setCurrentSlug($currentSlug) {
		$this->currentSlug = $currentSlug;
	}
	public function getCurrentAction() {
		return $this->currentAction;
	}
	// Set the current action, fetching the current route
	public function setCurrentAction() {
		$request = $this->container->get('request');
		$route = $request->get('_route');
		
		$action = $this->getCurrentActionByRoute($route);
		if($action !== null)
			$this->currentAction = $action;
	}
	public function getCurrentAdmin() {
		if($this->currentSlug !== null)
			return $this->admins[$this->currentSlug];
		else
			return null;
	}

	public function addAdmin($admin, $attributes = array()) {
		$slug = $attributes['slug'];
		$title = $attributes['title'];

		// Must get the specific service Admin to register its entityClass
		$resourceAdmin = $this->container->get($admin);
		$entityClass = $resourceAdmin->getEntityClass();

		// The admin array contains array(service, entityClass)
		$this->admins[$slug] = array(
				'service' 			=> $admin,
				'entityClass'		=> $entityClass
		);

		$resourceAdmin->setSlug($slug);
		$resourceAdmin->setTitle($title);
		// Generate the specific routes for this admin (only some array informations)
		$this->generateRoutes($slug);
	}

	public function addRoute($slug, $action, $path = null, $requirements = array(), $defaults = array()) {
		// We can specify the pattern of the path. Otherwise generate the default one
		if($path === null) {
			$path = $slug .'/'. $action;

			if(count($requirements) > 0) {
				foreach($requirements as $key => $requirement) {
					$path .= '/{'. $key .'}';
				}
			}
		}
			
		$this->routes[$slug][$action] = array(
			'route'				=> 'sfs_admin_'. $slug .'_'. $action,
			'action'			=> $action .'Action',
			'path'				=> $path,
			'requirements'		=> $requirements,
			'defaults'			=> $defaults
		);
	}

	private function generateRoutes($slug) {
		$this->addRoute($slug, 'list');
		$this->addRoute($slug, 'create');
		$this->addRoute($slug, 'read', null, array('id' => '\d+'));
		$this->addRoute($slug, 'update', null, array('id' => '\d+'));
		$this->addRoute($slug, 'delete', null, array('id' => '\d+'));
		$this->addRoute($slug, 'export', null, array('format' => '.+'), array('format' => 'csv'));
	}

	public function getRoutes() {
		return $this->routes;
	}

	public function getRouteBySlug($slug, $action) {
		if(!isset($this->routes[$slug][$action]))
			Throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException('The administration doesn\'t exist, so the route cannot be generated. Please create one.');
		return $this->routes[$slug][$action]['route'];
	}

	public function getRouteByEntity($object, $action) {
		$slug = $this->getAdminSlug($object);
		$route = $this->getRouteBySlug($slug, $action);

		return $route;
	}

	private function getCurrentActionByRoute($route) {
		$keyAction = array_search($route, array_column($this->routes[$this->getCurrentSlug()], 'route'));
		foreach($this->routes[$this->getCurrentSlug()] as $key => $action) {
			if($route == $action['route'])
				return $key;
		}

		return $keyAction;
	}

	public function getAdmins() {
		return $this->admins;
	}

	// Find the admin resource & class for a specific entity
	public function getAdmin($object) {
		$admins = $this->getAdmins();
		$class = ClassUtils::getClass($object);

		$keyAdmin = array_search($class, array_column($admins, 'entityClass'));

		return $keyAdmin;
	}
	// Find the admin slug knowing the object entity
	public function getAdminSlug($object) {
		$admins = $this->getAdmins();
		$class = ClassUtils::getClass($object);

		foreach($admins as $key => $admin) {
			if($admin['entityClass'] == $class)
				return $key;
		}

		Throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException('No administration found for the object of class '. $class .' please create one.');
	}

	// Generate an admin url for an entity
	public function getUrl($slug, $action, array $parameters = array()) {
		$route = $this->getRouteBySlug($slug, $action);

		if(isset($parameters['object'])) {
			$parameters['id'] = $parameters['object']->getId();
			unset($parameters['object']);
		}
		// Only generate a route with parameters if required
		return $this->generateUrl($route, $parameters);
	}

	public function generateUrl($route, array $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
	{
		return $this->container->get('router')->generate($route, $parameters, $referenceType);
	}
}
