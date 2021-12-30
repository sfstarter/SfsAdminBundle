<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('sfs_admin');
        $rootNode = $treeBuilder->getRootNode();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
		$rootNode
        ->children()
	        ->arrayNode('menu_categories')
	            ->isRequired()
	            ->prototype('array')
	                ->children()
	                    ->scalarNode('name')->isRequired()->end()
	                    ->scalarNode('icon')->isRequired()->end()
	                ->end()
				->end()
			->end()
			->arrayNode('pages')
				->prototype('array')
					->children()
						->scalarNode('route')->isRequired()->end()
						->scalarNode('title')->isRequired()->end()
						->scalarNode('slug')->isRequired()->end()
						->scalarNode('category')->isRequired()->end()
						->scalarNode('icon')->isRequired()->end()
					->end()
				->end()
			->end()
			->arrayNode('topbar_buttons')
				->prototype('array')
					->children()
						->scalarNode('title')->isRequired()->end()
						->scalarNode('route')->defaultNull()->end()
						->scalarNode('url')->defaultNull()->end()
						->scalarNode('icon')->isRequired()->end()
					->end()
				->end()
			->end()
            ->scalarNode('routes_prefix')->defaultValue('sfs_admin')->end()
            ->scalarNode('title_text')->defaultValue('Sfs Admin')->end()
            ->scalarNode('title_logo')->defaultNull()->end()
		->end();

		return $treeBuilder;
    }
}
