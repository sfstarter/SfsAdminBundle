<?php
/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 * Date: 27/04/2016
 * Time: 18:50
 */

namespace Sfs\AdminBundle\Tests\Controller;

use Sfs\AdminBundle\Tests\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class PageControllerTest extends Client
{
	/**
	 * testDashboardActionSuccess
	 */
	public function testDashboardActionSuccess()
	{
		$url = 'http://'. $this->host .'/admin/dashboard';
		$this->authentifiedRequest('GET', $url);

		// Test if code is 200
		$this->assertTrue($this->client->getResponse()->isSuccessful());
	}

	/**
	 * testDashboardActionAnonymously
	 */
	public function testDashboardActionAnonymously()
	{
		$url = 'http://'. $this->host .'/admin/dashboard';
		$this->anonymRequest('GET', $url);

		// Test if code is 302 to login page
		$this->assertTrue($this->client->getResponse()->isRedirect('http://'. $this->host .'/admin/login'));
	}

	/**
	 * testNotFoundUrl
	 */
	public function testNotFoundUrl() {
		$url = 'http://'. $this->host .'/admin/fake/url';
		$this->authentifiedRequest('GET', $url);

		// Test if code is 404
		$this->assertTrue($this->client->getResponse()->isNotFound());
	}
}
