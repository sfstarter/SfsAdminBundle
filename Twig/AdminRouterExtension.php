<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Twig;

use Sfs\AdminBundle\Core\CoreAdmin;

class AdminRouterExtension extends \Twig_Extension {
	/**
	 * @var CoreAdmin
	 */
	protected $core;

	/**
	 * 
	 * @param CoreAdmin $core
	 */
	public function __construct(CoreAdmin $core) {
		$this->core = $core;
	}

	/**
	 * getFunctions
	 * 
	 * @return array
	 */
	public function getFunctions() {
		return array (
				'admin_url' => new \Twig_Function_Method($this, 'getAdminUrl'),
				'admin_route' => new \Twig_Function_Method($this, 'getAdminRoute')
		);
	}

	/**
	 * getAdminUrl
	 * 
	 * @param string $action
	 * @param array $parameters
	 * 
	 * @return string
	 */
	public function getAdminUrl($action, array $parameters = array()) {
		if(isset($parameters['object'])) {
			$slug = $this->core->getAdminSlug($parameters['object']);
			$url = $this->core->getUrl($slug, $action, $parameters);
			return $url;
		}
		else {
			$slug = $this->core->getCurrentSlug();
			if($slug) {
				$url = $this->core->getUrl($slug, $action, $parameters);
				return $url;
			}
		}
	}

	/**
	 * @deprecated 
	 */
	public function getAdminRoute($action, $object = null) {
		if($object !== null) {
			$url = $this->core->getRouteByEntity($object, $action);
			return $url;
		}
	}

	/**
	 * getName
	 * 
	 * @return string
	 */
	public function getName() {
		return 'twig_sfs_admin_router';
	}
}
