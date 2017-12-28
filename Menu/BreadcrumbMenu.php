<?php
/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 * Date: 27/12/17
 * Time: 16:39
 */

namespace Sfs\AdminBundle\Menu;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class BreadcrumbMenu implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * breadcrumbMenu
     *
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function breadcrumbMenu(FactoryInterface $factory, array $options) {
        $currentObject = $options['currentObject'];
        $translator = $this->container->get('translator');
        $core = $this->container->get('sfs.admin.core');
        $currentAdmin = $core->getCurrentAdmin();

        $menu = $factory->createItem('breadcrumb', array(
            'childrenAttributes' => array(
                'class' => 'breadcrumb pull-left mt-8 mb-8'
            )
        ));
        $menu->addChild($translator->trans('sfs.admin.page.dashboard'), array(
            'route' => 'sfs_admin_dashboard',
            'attributes' => array(
                'icon' => 'fa-home'
            )
        ));

        // Two possibilities: currently on an Admin Resource
        if($currentAdmin !== null) {
            $admin = $this->container->get($currentAdmin['service']);

            if($core->getCurrentAction() === 'list') {
                $menu->addChild($admin->getTitle() .' List');
            }
            else {
                $menu->addChild($admin->getTitle() .' List', array('route' => $core->getRouteBySlug($admin->getSlug(), 'list')));
            }
        }
        // Otherwise looking on a custom page
        else {

        }

        // If currently on a specific object
        if($currentObject !== null) {
            if(!method_exists($core->getCurrentEntityClass(), '__toString')) {
                throw new \RuntimeException(
                    'You must define the __toString method related to the entity '. $this->getCurrentEntityClass()
                );
            }
            $currentLabel = $translator->trans('sfs.admin.action.label.'. $core->getCurrentAction());
            $menu->addChild($currentObject->__toString());
            $menu->addChild($currentLabel);
        }

        return $menu;
    }
}
