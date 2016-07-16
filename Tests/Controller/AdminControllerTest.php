<?php
/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 * Date: 28/04/2016
 * Time: 21:43
 */

namespace Sfs\AdminBundle\Tests\Controller;

use Sfs\AdminBundle\Tests\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class AdminControllerTest extends Client
{
	/**
	 * testListActionSuccess
	 */
	public function testListActionSuccess()
	{
		$crawler = $this->requestListAction('pages');

		// Test if code is 200
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// Test if table contains correctly 10 elements
		$this->assertCount(10, $crawler->filter('.page-content .panel table tbody tr'));
	}

	/**
	 * testListActionFailure
	 */
	public function testListActionFailure()
	{
		$this->requestListAction('fake');

		// Test if code is 404
		$this->assertTrue($this->client->getResponse()->isNotFound());
	}

	/**
	 * testCreateActionSuccess
	 */
	public function testCreateActionSuccess()
	{
		$url = 'http://'. $this->host .'/admin/pages/create';
		$this->authentifiedRequest('GET', $url);

		// Test if code is 200
		$this->assertTrue($this->client->getResponse()->isSuccessful());
	}

	/**
	 * testCreateActionFailure
	 */
	public function testCreateActionFailure()
	{
		$url = 'http://'. $this->host .'/admin/fake/create';
		$this->authentifiedRequest('GET', $url);

		// Test if code is 404
		$this->assertTrue($this->client->getResponse()->isNotFound());
	}

	/**
	 * testUpdateActionSuccess
	 */
	public function testUpdateActionSuccess()
	{
		$id = $this->fixtures['page_1']->getId();

		$url = 'http://'. $this->host .'/admin/pages/update/'. $id;
		$this->authentifiedRequest('GET', $url);

		// Test if code is 200
		$this->assertTrue($this->client->getResponse()->isSuccessful());
	}

	/**
	 * testUpdateActionFailure
	 */
	public function testUpdateActionFailure()
	{
		$url = 'http://'. $this->host .'/admin/pages/update/999';
		$this->authentifiedRequest('GET', $url);

		// Test if code is 404
		$this->assertTrue($this->client->getResponse()->isNotFound());
	}

	/**
	 * testDeleteActionSuccess
	 */
	public function testDeleteActionSuccess()
	{
		$id = $this->fixtures['page_1']->getId();
		$crawler = $this->requestDeleteAction('pages', $id);

		// Test if page is 200
		$this->assertTrue($this->client->getResponse()->isSuccessful());

		// Test if the form validation redirects to the listAction page
		$form = $crawler->selectButton('delete[delete]')->form();
		$this->client->submit($form);

		$this->assertTrue($this->client->getResponse()->isRedirect('/admin/pages/list'));

		// Test if deletion is correct: the find method should return null
		$em = $this->client->getContainer()->get('doctrine');
		$repository = $em->getRepository('Sfs\AdminBundle\Tests\Fixtures\TestBundle\Entity\Page');
		$page = $repository->find($id);

		$this->assertNull($page);
	}

	/**
	 * testDeleteActionFailure
	 */
	public function testDeleteActionFailure()
	{
		$this->requestDeleteAction('fake', 1);

		// Test if page is 404
		$this->assertTrue($this->client->getResponse()->isNotFound());
	}


	/**
	 * @dataProvider exportFormatProvider
	 *
	 * @param string $format
	 * @param string $contentType
	 */
	public function testExportActionSuccess($format, $contentType)
	{
		$crawler = $this->requestListAction('pages');

		// Submit the modal form
		$form = $crawler->selectButton('export[download]')->form();
		$form->setValues(['export[format]' => $format]);

		// Send the form
		ob_start();
		$this->client->submit($form);
		// Need to clean the output_buffer, otherwise we receive the file content (not cool)
		ob_end_clean();

		// Test if page is 200
		$this->assertTrue($this->client->getResponse()->isSuccessful());
		// Test if the file is really a csv one
		$this->assertContains($contentType, $this->client->getResponse()->headers->get('Content-Type'), 'Wrong content type');
	}


	/**
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	private function requestListAction($slug) {
		$url = 'http://'. $this->host .'/admin/'. $slug .'/list';

		return $this->authentifiedRequest('GET', $url);
	}

	/**
	 * @param $slug
	 * @param $id
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	private function requestDeleteAction($slug, $id) {
		$url = 'http://'. $this->host .'/admin/'. $slug .'/delete/'. $id;

		return $this->authentifiedRequest('GET', $url);
	}

	/**
	 * @param $slug
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	private function requestExportAction($slug, $format) {
		$url = 'http://'. $this->host .'/admin/'. $slug .'/export';

		return $this->authentifiedRequest('POST', $url, array(
			'admin_export' => array(
				'download' => '',
				'fields' => array('id', 'title', 'content'),
				'format' => $format
			)
		));
	}

	/**
	 * Array of formats to be tested for export
	 *
	 * @return array
	 */
	public function exportFormatProvider() {
		return array(
			array('csv', 'text/csv'),
			array('json', 'application/json')
		);
	}
}
