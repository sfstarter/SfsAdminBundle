<?php
/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 * Date: 27/04/2016
 * Time: 22:22
 */

namespace Sfs\AdminBundle\Tests;

use Hautelook\AliceBundle\Alice\DataFixtures\Loader;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Nelmio\Alice\Fixtures;


class Client extends WebTestCase
{
	protected $host = 'sfsadmin.lh';
	protected $fixtures = null;

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Client
	 */
	protected $client = null;

	public function setUp()
	{
		$this->client = static::createClient(array(), array('HTTP_HOST' => $this->host));

		// Load all fixtures
		$this->fixtures = $this->loadFixtureFiles(array(
			'@SfsAdminBundle/Tests/Fixtures/DataFixtures/ORM/PageData.yml',
			'@SfsAdminBundle/Tests/Fixtures/DataFixtures/ORM/UserData.yml'
		));
	}

	/**
	 * @param $method
	 * @param $url
	 * @param array $parameters
	 * @param array $files
	 * @param array $server
	 * @param null $content
	 * @param bool $changeHistory
	 *
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	protected function anonymRequest($method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true) {
		return $this->client->request($method, $url, $parameters, $files, $server, $content, $changeHistory);
	}

	/**
	 * @param $method
	 * @param $url
	 * @param array $parameters
	 * @param array $files
	 * @param array $server
	 * @param null $content
	 * @param bool $changeHistory
	 * 
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	protected function authentifiedRequest($method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true) {
		$auth = array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'password',
		);

		$server = array_merge($server, $auth);
		return $this->client->request($method, $url, $parameters, $files, $server, $content, $changeHistory);
	}
}
