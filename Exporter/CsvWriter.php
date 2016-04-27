<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Exporter;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CsvWriter extends WriterAbstract
{
	/**
	 * Returns a streamed Response with the csv content of the exported entity
	 *
	 * @return StreamedResponse $response
	 */
	public function export() {
		$em = $this->em;
		$entityClass =  $this->entityClass;
		$listFields =  $this->listFields;

		$response = new StreamedResponse(function() use($em, $entityClass, $listFields) {
			// Get all objects in db
			$accessor = PropertyAccess::createPropertyAccessor();
			$repository = $em->getRepository($entityClass);
			$results = $repository->createQueryBuilder('o')->getQuery()->iterate();

			$handle = fopen('php://output', 'r+');

			// Header file
			foreach($listFields as $key => $field) {
				$row[] = $field;
			}
			fputcsv($handle, $row);

			// Content file
			while (false !== ($object = $results->next())) {
				$row = array();
				// Default export uses the vars defined in setListFields
				foreach($listFields as $key => $field) {
					$property = $accessor->getValue($object[0], $field);

					// Date object must be formatted
					if(is_object($property) && get_class($property) == 'DateTime') {
						$row[] = $property->format('Y-m-d H:i:s');
					}
					// Arrays
					else if(is_array($property)) {
						$row[] = json_encode($property);
					}
					// Same for the 1TM & 1TM & MTM relations
					else if(is_object($property) && get_class($property) == 'Doctrine\ORM\PersistentCollection') {
						$value = '';
						foreach($property->getIterator() as $child) {
							$value .=  $child->__toString();
							
							if($child != $property->last()) {
								$value .= ' & ';
							}
						}
						$row[] = $value;
					}
					// For other type of vars, we can just insert them
					else {
						$row[] = $property;
					}
				}
				fputcsv($handle, $row);
				// utilisé pour limiter la consommation de mémoire
				$em->detach($object[0]);
			}

			fclose($handle);
		});

		return $response;
	}
}
