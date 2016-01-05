<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Exporter;

use Symfony\Component\HttpFoundation\StreamedResponse;

class JsonWriter extends WriterAbstract
{
	/**
	 * Returns a streamed Response with the json content of the exported entity
	 * 
	 * @return StreamedResponse $response
	 */
	public function export() {
		$em = $this->em;
		$entityClass =  $this->entityClass;
		$listFields =  $this->listFields;

		$response = new StreamedResponse(function() use($em, $entityClass, $listFields) {
			// Get all objects in db
			$repository = $em->getRepository($entityClass);
			$results = $repository->createQueryBuilder('o')->getQuery()->getArrayResult();

			$handle = fopen('php://output', 'r+');

			fwrite($handle, json_encode($results));

			fclose($handle);
		});

		return $response;
	}
}
