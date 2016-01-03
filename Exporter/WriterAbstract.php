<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Exporter;

use Symfony\Component\DependencyInjection\ContainerAware;

abstract class WriterAbstract extends ContainerAware
{
	protected $em;
	protected $entityClass;
	protected $listFields;

	public function __construct($em, $entityClass, $listFields) {
		$this->em = $em;
		$this->entityClass = $entityClass;
		$this->listFields = $listFields;
	}

	abstract public function export();
}
