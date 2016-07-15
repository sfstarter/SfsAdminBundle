<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Exporter;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class WriterAbstract implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var string
	 */
	protected $entityClass;

	/**
	 * @var array
	 */
	protected $listFields;

	/**
	 * 
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param string $entityClass
	 * @param array $listFields
	 */
	public function __construct($em, $entityClass, $listFields) {
		$this->em = $em;
		$this->entityClass = $entityClass;
		$this->listFields = $listFields;
	}

	/**
	 * Returns the export file
	 * 
	 */
	abstract public function export();
}
