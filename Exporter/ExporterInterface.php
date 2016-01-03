<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Exporter;

interface ExporterInterface
{
	public static function getResponse($em, $format, $filename = null, $entityClass, $listFields);
}
