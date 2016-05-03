<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Core;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\Common\Util\ClassUtils;

class CoreAdmin extends ContainerAware
{
	/**
	 * Array of every admin resources available (should look like array('slug' => array(service, entityClass), 'slug' => array(service, entityClass), ...)
	 * 
	 * @var array
	 */
	protected $admins = array();

	/**
	 * Every damn generated routes are registered here (should look like array('slug' => array('action' => array('route', 'action', 'path', 'requirements'))) )
	 * 
	 * @var array
	 */
	protected $routes = array();

	/**
	 * Slug of the current admin resource
	 * 
	 * @var string
	 */
	private $currentSlug = null;

	/**
	 * Action of the current view. It can be list, create ...
	 * 
	 * @var string
	 */
	private $currentAction = null;

	/**
	 * getCurrentSlug
	 * 
	 * @return string $currentSlug
	 */
	public function getCurrentSlug() {
		return $this->currentSlug;
	}
	/**
	 * setCurrentSlug
	 * 
	 * @param string $currentSlug
	 * 
	 * @return CoreAdmin $this
	 */
	public function setCurrentSlug($currentSlug) {
		$this->currentSlug = $currentSlug;

		return $this;
	}

	/**
	 * getCurrentAction
	 * 
	 * @return string currentAction
	 */
	public function getCurrentAction() {
		return $this->currentAction;
	}

	/** 
	 * Set the current action, fetching the current route
	 */
	public function setCurrentAction() {
		$request = $this->container->get('request');
		$route = $request->get('_route');
		
		$action = $this->getCurrentActionByRoute($route);
		if($action !== null)
			$this->currentAction = $action;
	}

	/**
	 * getCurrentAdmin
	 * 
	 * @return array|null
	 */
	public function getCurrentAdmin() {
		if($this->currentSlug !== null)
			return $this->admins[$this->currentSlug];
		else
			return null;
	}

	/**
	 * getCurrentEntityClass
	 *
	 * @return array|null
	 */
	public function getCurrentEntityClass() {
		if($this->currentSlug !== null)
			return $this->admins[$this->currentSlug]['entityClass'];
			else
				return null;
	}

	/**
	 * Registers the name of an admin service & the attributes of the service
	 * 
	 * @param string $admin
	 * @param array $attributes
	 */
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

	/**
	 * Add a new route for a specific admin resource
	 * 
	 * @param string $slug
	 * @param string $action
	 * @param string $path
	 * @param array $requirements
	 * @param array $defaults
	 */
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

	/**
	 * Generate every routes for a specific admin resource
	 * 
	 * @param string $slug
	 */
	private function generateRoutes($slug) {
		$this->addRoute($slug, 'list');
		$this->addRoute($slug, 'create');
		$this->addRoute($slug, 'read', null, array('id' => '\d+'));
		$this->addRoute($slug, 'update', null, array('id' => '\d+'));
		$this->addRoute($slug, 'delete', null, array('id' => '\d+'));
		$this->addRoute($slug, 'export');
		$this->addRoute($slug, 'batch');
	}

	/**
	 * Get all generated routes
	 * 
	 * @return array $routes
	 */
	public function getRoutes() {
		return $this->routes;
	}

	/**
	 * Get a route by a specified slug of admin resource, & its action
	 * 
	 * @param $slug
	 * @param $action
	 * @return mixed
	 * @throws ResourceNotFoundException
	 */
	public function getRouteBySlug($slug, $action) {
		if(!isset($this->routes[$slug][$action]))
			Throw new ResourceNotFoundException('The administration doesn\'t exist, so the route cannot be fetch. Please create one.');
		return $this->routes[$slug][$action]['route'];
	}

	/**
	 * Get a route knowing a specific entity, so we got to find its related admin resource. & the action
	 * 
	 * @param mixed $object
	 * @param string $action
	 * 
	 * @return string
	 */
	public function getRouteByEntity($object, $action) {
		$slug = $this->getAdminSlug($object);
		$route = $this->getRouteBySlug($slug, $action);

		return $route;
	}

	/**
	 * Reverses the engine to get the current action, knowing the current route
	 * 
	 * @param string $route
	 * 
	 * @return string
	 */
	private function getCurrentActionByRoute($route) {
		$keyAction = array_search($route, array_column($this->routes[$this->getCurrentSlug()], 'route'));
		foreach($this->routes[$this->getCurrentSlug()] as $key => $action) {
			if($route == $action['route'])
				return $key;
		}

		return $keyAction;
	}

	/**
	 * Get all the registered admin resources
	 * 
	 * @return array
	 */
	public function getAdmins() {
		return $this->admins;
	}

	/**
	 * Get the admin resource for a specific entity
	 * 
	 * @param mixed $object
	 * 
	 * @return string
	 */ 
	public function getAdmin($object) {
		$admins = $this->getAdmins();
		$class = ClassUtils::getClass($object);

		$keyAdmin = array_search($class, array_column($admins, 'entityClass'));

		return $keyAdmin;
	}

	/**
	 * Get the admin resource
	 *
	 * @param $slug
	 * @return null|object
	 */
	public function getAdminService($slug) {
		if(isset($this->admins[$slug])) {
			$service = $this->admins[$slug]['service'];

			return $this->container->get($service);
		}
		else {
			return null;
		}
	}

	/**
	 * Get the admin slug knowing the object entity
	 * 
	 * @param mixed $object
	 * 
	 * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException
	 * 
	 * @return string
	 */
	public function getAdminSlug($object) {
		$admins = $this->getAdmins();
		$class = ClassUtils::getClass($object);

		foreach($admins as $key => $admin) {
			if($admin['entityClass'] == $class)
				return $key;
		}

		Throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException('No administration found for the object of class '. $class .' please create one.');
	}

	/**
	 * Generate an admin url for an entity
	 * 
	 * @param string $slug
	 * @param string $action
	 * @param array $parameters
	 * 
	 * @return string $url
	 */
	public function getUrl($slug, $action, array $parameters = array()) {
		$route = $this->getRouteBySlug($slug, $action);

		if(isset($parameters['object'])) {
			$slug = $this->getAdminSlug($parameters['object']);
			$identifier = $this->getAdminService($slug)->getIdentifierProperty();
			$parameters['id'] = $parameters['object']->{'get'. ucfirst($identifier)}();
			unset($parameters['object']);
		}
		// Only generate a route with parameters if required
		return $this->generateUrl($route, $parameters);
	}

	/**
	 * Generate a url knowing the route & the parameters to associate to it.
	 * Possibility to set if the url has to be relative or absolute
	 * 
	 * @param string $route
	 * @param array $parameters
	 * @param const int $referenceType
	 * 
	 * @return string $url
	 */
	public function generateUrl($route, array $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
	{
		return $this->container->get('router')->generate($route, $parameters, $referenceType);
	}
}
