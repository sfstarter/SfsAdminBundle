<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ResourceCompilerPass implements CompilerPassInterface
{
	/**
	 * Configure core with all admin services
	 * 
	 * @param ContainerBuilder $container
	 */
	public function process(ContainerBuilder $container)
	{
		if (!$container->hasDefinition('sfs.admin.routing_loader') || !$container->hasDefinition('sfs.admin.menu_builder')) {
			return;
		}

		$core = $container->getDefinition('sfs.admin.core');
		$sidebarBuilder = $container->getDefinition('sfs.admin.menu_builder');

		/**
		 * Handle the admin Resources, connected to an entity
		 */
        $taggedServices = $container->findTaggedServiceIds('sfs_admin.resource');
        foreach ($taggedServices as $id => $tagAttributes) {
        	// id is the current admin's service name with the tag 'sfs_admin.resource' and being looped
        	foreach ($tagAttributes as $attributes) {
				$container->getDefinition($id)->addMethodCall('setContainer', array(new Reference('service_container')));
				$container->getDefinition($id)->addMethodCall('setFilterForm');
				$core->addMethodCall('addAdmin', array($id, $attributes));
				$sidebarBuilder->addMethodCall('addResource', array($attributes));
        	}
		}
	}
}
