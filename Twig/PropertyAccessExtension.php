<?php
/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Twig;

use Twig_SimpleFunction;
use Symfony\Component\PropertyAccess\PropertyAccess;

class PropertyAccessExtension extends \Twig_Extension {

	/**
	 * PropertyAccessExtension constructor.
	 */
	public function __construct() {
	}

	/**
	 * getFunctions
	 *
	 * @return array
	 */
	public function getFunctions() {
		return array(
			new Twig_SimpleFunction('admin_get_property', array($this, 'getProperty')),
		);
	}

	/**
	 * getProperty
	 *
	 * @param mixed $object
	 * @param string $property
	 * @return null|mixed
	 */
	public function getProperty($object, $property) {
		$value = null;

		if(!$object) {
			return $value;
		}
		$accessor = PropertyAccess::createPropertyAccessor();

		if($object) {
			try {
				$value = $accessor->getValue($object, $property);
			} catch (\Exception $e) {
			}
		}

		return $value;
	}
}
