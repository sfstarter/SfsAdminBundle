<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SfsAdminExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('sfs_admin.menu_categories', $config['menu_categories']);
        $container->setParameter('sfs_admin.pages', $config['pages']);
        $container->setParameter('sfs_admin.topbar_buttons', $config['topbar_buttons']);
        $container->setParameter('sfs_admin.title_text', $config['title_text']);
        $container->setParameter('sfs_admin.title_logo', $config['title_logo']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('forms.yml');
    }

    /**
     * Configure the related bundles so that the user doesn't need to write those lines itself
     * 
     * @param ContainerBuilder $container
     * @throws \RuntimeException
     */
	public function prepend(ContainerBuilder $container)
	{
		// get all bundles
		$bundles = $container->getParameter('kernel.bundles');

		/**
		 * MopaBootstrapBundle Configuration
		 * The user can still edit it, but it might break the forms inside the SfsAdminBundle
		 */
		if (!isset($bundles['MopaBootstrapBundle'])) {
			throw new \RuntimeException(
					'The MopaBootstrapBundle is required to generate the forms for the SfsAdmin Bundle'
			);
		}
		foreach ($container->getExtensions() as $name => $extension) {
			switch ($name) {
				case 'mopa_bootstrap':
					$config = array(
							'menu' => array(),
							'form' => array(
									'render_fieldset' => false, // default is true
									'show_legend' => false, // default is true
									'render_optional_text' => false, // default is true
									'render_required_asterisk' => true
							)
					);
					$container->prependExtensionConfig($name, $config);
				break;
				case 'twig':
					$config = array(
							'form' => array(
									'resources' => array(
										'SfsAdminBundle:Form:fields.html.twig'
									)
							)
					);
					$container->prependExtensionConfig($name, $config);
				break;
			}
        }
	}
}
