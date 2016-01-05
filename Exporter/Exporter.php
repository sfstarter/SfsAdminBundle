<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Exporter;

class Exporter implements ExporterInterface
{
	/**
	 * Returns a streamed response depending on the format required : calls a different writer for each format
	 * 
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param string $format
	 * @param string $filename
	 * @param string $entityClass
	 * @param array $listFields
	 * 
	 * @throws \RuntimeException
	 * 
	 * @return \Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public static function getResponse($em, $format = 'csv', $filename = null, $entityClass, $listFields) {
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
