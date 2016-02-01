<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */


namespace Sfs\AdminBundle\Twig;

use Doctrine\ORM\EntityManager;
use Sfs\AdminBundle\Core\CoreAdmin;

class OfTypeExtension extends \Twig_Extension {

	/**
	 * @var CoreAdmin
	 */
	private $core;

	/**
	 * @var entityManager
	 */
	private $entityManager;

	/**
	 *
	 * @param CoreAdmin $core
	 */
	public function __construct(EntityManager $entityManager, CoreAdmin $core) {
		$this->entityManager = $entityManager;
		$this->core = $core;
	}

	/**
	 * getTests
	 * 
	 * @return array
	 */
	public function getTests() {
		return array (
				'property_is_relation' => new \Twig_Function_Method($this, 'PropertyIsRelation'),
				'of_type' => new \Twig_Function_Method($this, 'isOfType')
		);
	}

	/**
	 * getFilters
	 * 
	 * @return array
	 */
	public function getFilters() {
		return array('get_type' => new \Twig_Filter_Method($this, 'getType'));
	}

	/**
	 * Check if a named property is of specified type.
	 * Call when we only know the name of property, and not the var itself
	 *
	 */
	public function PropertyIsRelation($propertyName, $entityClass=null) {
		if($entityClass === null) {
			$entityClass = $this->core->getCurrentEntityClass();
		}

		return $this->entityManager->getMetadataFactory()->getMetadataFor($entityClass)->hasAssociation($propertyName);
	}

	/**
	 * isOfType
	 * 
	 * @param mixed $var
	 * @param string $typeTest
	 * @param string $className
	 *
	 * @return bool
	 */
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
				if(is_object($var) === true) {
					if($className !== null) {
						return get_class($var) == $className;
					}

					return true;
				}
				else {
					return false;
				}
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

	/**
	 * getType
	 * 
	 * @param mixed $var
	 * 
	 * @return string
	 */
	public function getType($var) {
		if(!is_object($var)) {
			return gettype($var);
		}
		else {
			return get_class($var);
		}
	}

	/**
	 * getName
	 * 
	 * @return string
	 */
	public function getName() {
		return 'twig_sfs_admin_of_type';
	}
}
