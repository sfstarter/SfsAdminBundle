<?php
/**
 * Yasa - Symfony3 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 * Date: 01/04/18
 * Time: 18:15
 */

namespace Sfs\AdminBundle\Twig;

use Sfs\AdminBundle\Core\CoreAdmin;
use Twig_SimpleFunction;

class AdminTemplateExtension extends \Twig_Extension {
    /**
     * @var CoreAdmin
     */
    protected $core;

    /**
     *
     * @param CoreAdmin $core
     */
    public function __construct(CoreAdmin $core) {
        $this->core = $core;
    }

    /**
     * getFunctions
     *
     * @return array
     */
    public function getFunctions() {
        return array (
            new Twig_SimpleFunction('admin_get_template', array($this, 'getAdminTemplate')),
        );
    }

    /**
     * @return string
     */
    public function getAdminTemplate($object, $slug) {
        if($object !== null && is_string($object)) {
            $object = new $object;
        }

        if($object !== null && is_object($object) && is_string($slug)) {
            $adminSlug = $this->core->getAdminSlug($object);

            $template = $this->core->getAdminService($adminSlug)->getTemplate($slug);
            return $template;
        }
    }
}