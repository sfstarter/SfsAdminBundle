<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Exporter;

interface ExporterInterface
{
	/**
	 * getResponse
	 * 
	 * @param unknown $em
	 * @param string $format
	 * @param string $filename
	 * @param string $entityClass
	 * @param array $listFields
	 */
	public static function getResponse($em, $format, $filename = null, $entityClass, $listFields);
}
