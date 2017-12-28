<?php
/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 * Date: 28/12/17
 * Time: 19:10
 */

namespace Sfs\AdminBundle\Menu;

use Sfs\AdminBundle\Controller\AdminController;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class SubBarMenu implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * subBarMenu
     *
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function subBarMenu(FactoryInterface $factory, array $options) {
        $currentObject = $options['currentObject'];
        $translator = $this->container->get('translator');

        $core = $this->container->get('sfs.admin.core');
        /** @var AdminController $currentAdmin */
        $adminSlug = $core->getAdminSlug($currentObject);
        $currentAdmin = $core->getAdminService($adminSlug);
        $entryActions = $currentAdmin->getEntryActions();

        $menu = $factory->createItem('subbar', array(
            'childrenAttributes' => array(
                'class' => 'nav navbar-nav'
            )
        ));

        foreach($entryActions as $entryAction) {
            $menu->addChild(
                $translator->trans('sfs.admin.action.label.'. $entryAction),
                array(
                    'route' 	=> $currentAdmin->getRoute($entryAction),
                    'routeParameters' => array('id' => $currentObject->getId()),
                    'attributes' => array(
                        'showLabel' => true
                    )
                )
            );
        }

        return $menu;
    }
}
