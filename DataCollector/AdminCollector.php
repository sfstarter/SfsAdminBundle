<?php
/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 * Date: 01/05/2016
 * Time: 09:27
 */

namespace Sfs\AdminBundle\DataCollector;

use Sfs\AdminBundle\Core\CoreAdmin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class AdminCollector extends DataCollector
{
	/**
	 * @var CoreAdmin
	 */
	private $core;

	/**
	 * AdminCollector constructor.
	 * @param CoreAdmin $core
	 */
	public function __construct(CoreAdmin $core)
	{
		$this->core = $core;
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param \Exception|null $exception
	 */
	public function collect(Request $request, Response $response, \Exception $exception = null)
	{
		$this->data = array(
			'adminsTotal' => count($this->core->getAdmins()),
			'admins' => $this->core->getAdmins(),
			'routes' => $this->core->getRoutes(),
			'currentAdminService' => $this->core->getCurrentAdmin()['service'],
			'currentAdminSlug' => $this->core->getCurrentSlug(),
			'currentEntityClass' => $this->core->getCurrentEntityClass(),
			'currentAction' => $this->core->getCurrentAction(),
		);
	}

	/**
	 * @return integer
	 */
	public function getAdminsTotal()
	{
		return $this->data['adminsTotal'];
	}

	/**
	 * @return array
	 */
	public function getAdmins()
	{
		return $this->data['admins'];
	}

	/**
	 * @return array
	 */
	public function getRoutes() {
		return $this->data['routes'];
	}

	/**
	 * @return string
	 */
	public function getCurrentAdminService()
	{
		return $this->data['currentAdminService'];
	}

	/**
	 * @return string
	 */
	public function getCurrentAdminSlug()
	{
		return $this->data['currentAdminSlug'];
	}

	/**
	 * @return string
	 */
	public function getCurrentEntityClass()
	{
		return $this->data['currentEntityClass'];
	}

	/**
	 * @return string
	 */
	public function getCurrentAction()
	{
		return $this->data['currentAction'];
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'sfs.admin.admin_collector';
	}
}
