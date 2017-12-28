<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Menu;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sfs\AdminBundle\Menu\Topbar\TopbarBlockInterface;

class MenuBuilder implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * Contains all admin resources to be displayed on the menu
	 * 
	 * @var array
	 */
	protected $adminResources;

	/**
	 * Contains all topbar blocks, by the name of the services, to be displayed in the topbar menu
	 * @var array
	 */
	protected $topbarBlocks;

	/**
	 * 
	 * @var FactoryInterface
	 */
	private $factory;

	/**
	 * 
	 * @param FactoryInterface $factory
	 */
	public function __construct(FactoryInterface $factory) {
		$this->adminResources = array();
		$this->adminPages = array();
		$this->factory = $factory;
	}

	/**
	 * Register a new admin resource, to be used when the menu is displayed
	 * @param unknown $attributes
	 */
	public function addResource($attributes) {
		$this->adminResources[] = array(
			'slug' => $attributes['slug'],
			'title' => $attributes['title'],
			'category' => $attributes['category'],
			'icon' => $attributes['icon']
		);
	}

    /**
     * Register a new topbar block, to be used when the topbar menu is displayed
     * @param array $attributes
     */
	public function addTopbarBlock($service) {
		$this->topbarBlocks[] = $service;
	}

	/**
     * sidebarMenu
	 *
	 * @param RequestStack $requestStack
	 * @return \Knp\Menu\ItemInterface
	 */
	public function sidebarMenu(RequestStack $requestStack) {
		$routes = $this->container->get('sfs.admin.core')->getRoutes();
		$categories = $this->container->getParameter('sfs_admin.menu_categories');
		$pages = $this->container->getParameter('sfs_admin.pages');

		$menu = $this->factory->createItem('sidebar', array(
			'childrenAttributes'    => array(
				'class'             => 'nav nav-sidebar mt-32'
	    	)
		));

		foreach($categories as $category) {
			$idCategory = strtolower(preg_replace('/\s+/', '_', $category['name']));
			$menu->addChild($category['name'], array(
					'uri' => 'javascript:void(0);',
					'linkAttributes' => array(
						'data-toggle' => 'collapse',
						'data-target' => '#'.$idCategory
					),
					'childrenAttributes' => array(
						'id' => $idCategory,
						'class'		=> 'nav nav-second-level collapse'
					),
					'attributes' => array(
						'icon' => $category['icon']
					)
			));

			foreach($this->adminResources as $resource) {
				if($resource['category'] == $category['name']) {
					$slug = $resource['slug'];
					$menu[$category['name']]->addChild(
							$resource['title'],
							array('route' 	=> $routes[$slug]['list']['route'],
							'attributes' => array(
									'icon' => $resource['icon']
							)
					));
				}
			}
			foreach($pages as $page) {
				if($page['category'] == $category['name']) {
					$slug = $resource['slug'];
					$menu[$category['name']]->addChild(
							$page['title'],
							array('route' 	=> $page['route'],
									'attributes' => array(
											'icon' => $page['icon']
									)
							));
				}
			}
		}

		return $menu;
	}

	/**
	 * topbarMenu contains the twig file to display the user dropdown
	 *
	 * @param RequestStack $requestStack
	 *
	 * @return \Knp\Menu\ItemInterface
	 */
	public function topbarMenu(RequestStack $requestStack) {
		$buttons = $this->container->getParameter('sfs_admin.topbar_buttons');

		$menu = $this->factory->createItem('topbar', array(
				'childrenAttributes' => array(
						'class' => 'nav navbar-nav navbar-right'
				)
		));

		foreach($buttons as $button) {
			if($button['route']) {
				$menu->addChild($button['title'], array(
						'route' 	=> $button['route'],
						'attributes' => array(
							'icon' => $button['icon']
						)
				));
			}
			else if($button['url']) {
				$menu->addChild($button['title'], array(
						'uri' 	=> $button['url'],
						'attributes' => array(
							'icon' => $button['icon']
						),
						'linkAttributes' => array(
							'target' => '_blank'
						)
				));
			}
		}

		$this->displayTopbarBlocks($menu);

		return $menu;
	}

    /**
	 * Display in the Topbar menu all the blocks services tagged as sfs_admin.menu.topbar
	 *
	 * @param ItemInterface $menu
	 */
	private function displayTopbarBlocks(ItemInterface $menu) {
		foreach ($this->topbarBlocks as $service) {
			$block = $this->container->get($service);

			// Only consider the true topbar blocks : they must implement InterfaceTopbarBlock
			if($block instanceof TopbarBlockInterface) {
				$htmlContent = $block->display();

				$menu->addChild($htmlContent, array(
						'attributes'	=> $block->getAttributes(),
						'extras' 		=> array(
							'noSpan' 		=> true,
							'safe_label' => true
						)
				));
			}
		}
	}
}
