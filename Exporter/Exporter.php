<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Exporter;

class Exporter implements ExporterInterface
{
	public static function getResponse($format = 'csv', $filename = null, $em, $entityClass, $listFields) {
		if($filename === null)
			$filename = 'export';

		switch ($format) {
			case 'csv':
				$writer      = new CsvWriter($em, $entityClass, $listFields);
				$contentType = 'text/csv';
				break;
			case 'json':
				$writer      = new JsonWriter($em, $entityClass, $listFields);
				$contentType = 'application/json';
				break;
			default:
				throw new \RuntimeException('File format unknown');
		}

		$response = $writer->export();

		$response->headers->set('Content-Type', $contentType);
		$response->headers->set('Content-Disposition','attachment; filename="'. $filename .'.'. $format .'"');

		return $response;
	}
}
