<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */


namespace Sfs\AdminBundle\Twig;

class OfTypeExtension extends \Twig_Extension {
	public function getTests() {
		return array (
				'of_type' => new \Twig_Function_Method($this, 'isOfType')
		);
	}
	public function getFilters() {
		return array('get_type' => new \Twig_Filter_Method($this, 'getType'));
	}

	public function isOfType($var, $typeTest=null, $className=null) {
		switch ($typeTest) {
			default:
				return false;
				break;
			case 'array':
				return is_array($var);
				break;
			case 'bool':
				return is_bool($var);
				break;
			case 'class':
				return is_object($var) === true && get_class($var) == $className;
				break;
			case 'float':
				return is_float($var);
				break;
			case 'int':
				return is_int($var);
				break;
			case 'numeric':
				return is_numeric($var);
				break;
			case 'object':
				return is_object($var);
				break;
			case 'scalar':
				return is_scalar($var);
				break;
			case 'string':
				return is_string($var);
				break;
		}
	}

	public function getType($var) {
		if(!is_object($var)) {
			return gettype($var);
		}
		else {
			return get_class($var);
		}
	}

	public function getName() {
		return 'twig_sfs_admin_of_type';
	}
}
