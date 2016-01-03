<?php

/**
 * SfsAdminBundle - Symfony2 project
 * 
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

use Sfs\AdminBundle\Exporter\Exporter;
use Sfs\AdminBundle\Form\DeleteType;

abstract class AdminController extends Controller
{
	protected $slug;
	protected $title;
	protected $entityClass;
	protected $filterForm;

	// Used to keep a track of relations and persist them (bonus point for the one who finds a better fix)
	private $associations;
	private $relations;

	abstract protected function setUpdateForm($object);

	public function __construct($entityClass) {
		$this->entityClass = $entityClass;
	}

	/**
	 * Creates and returns a Form instance from the type of the form (override of the Symfony default)
	 *
	 * @param string|FormTypeInterface $type    The built type of the form
	 * @param mixed                    $data    The initial data for the form
	 * @param array                    $options Options for the form
	 *
	 * @return Form
	 */
	public function createAdminForm($type, $data = null, array $options = array())
	{
		return $this->container->get('sfs_admin.form.factory')->create($type, $data, $options);
	}

	// Default list array is resumed by it's ID and the __toString value
	public function setListFields() {
		if(!method_exists($this->entityClass, '__toString')) {
			throw new \RuntimeException(
				'You must define the __toString method related to the entity '. $this->entityClass
			);
		}

		return array(
				'id' 			=> array('name' => 'ID'),
				'__toString' 	=> array('name' => 'Value'),
		);
	}
	public function listAction(Request $request) {
		$em = $this->container->get('doctrine')->getManager();
		$query = $em->getRepository($this->entityClass)->createQueryBuilder('object');

		// Filter form
		if($this->filterForm !== null) {
			$this->filterForm->handleRequest($request);
			// build the query filter
			if ($this->filterForm->isValid()) {
				$query = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($this->filterForm, $query);
			}
			
			$viewFilterForm = $this->filterForm->createView();
		}
		else
			$viewFilterForm = null;

		$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query, /* query applied */
				$request->query->getInt('page', 1)/* page number */,
				10/* limit per page */
		);
		$pagination->setPageRange(4);

		$listFields = $this->setListFields();

		return $this->render('SfsAdminBundle:CRUD:list.html.twig', array(
				'filterForm' => $viewFilterForm,
				'listFields'	=> $listFields,
				'pagination' => $pagination
		));
	}
	public function setFilterForm() {
		$this->filterForm = null;
	}

	// Default mode the create form is the same as the update one
	public function setCreateForm($object) {
		return $this->setUpdateForm($object);
	}

	// Resolves oneToMany relations by keeping them inside an array
	private function parseAssociations($object) {
		$this->associations = $this->getMetadata(get_class($object))->getAssociationMappings();

		if ($this->associations) {
			foreach ($this->associations as $field => $mapping) {
				$this->relations[$field] = array();
				if ($owningObjects = $object->{'get' . ucfirst($mapping['fieldName'])}()) {
					foreach ($owningObjects as $owningObject) {
						$this->relations[$field][] = $owningObject;
					}
				}
			}
		}
	}

	public function persistAssociations($em, $object) {
		if ($this->associations) {
			foreach ($this->associations as $field => $mapping) {
				if ($mapping['isOwningSide'] == false) {
					if ($owningObjects = $object->{'get' . ucfirst($mapping['fieldName'])}()) {
						// Set to null the original ones, not contained in the new object
						foreach ($this->relations[$field] as $owningObject) {
							if (false === $object->{'get' . ucfirst($mapping['fieldName'])}()->contains($owningObject)) {
								$owningObject->{'set' . ucfirst($mapping['mappedBy']) }(null);
								$em->persist($owningObject);
							}
						}

						// Set the new relations
						foreach ($owningObjects as $owningObject) {
							$owningObject->{'set' . ucfirst($mapping['mappedBy']) }($object);
							$em->persist($owningObject);
						}
					}
				}
			}
		}
	}
	// Persist function called when sending an update form
	public function persistCreate($em, $object) {
		$this->persistUpdate($em, $object);
	}
	public function createAction(Request $request) {
		$object = new $this->entityClass();
		$this->parseAssociations($object);

		$form = $this->setCreateForm($object);

		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->container->get('doctrine')->getManager();

			$this->persistAssociations($em, $object);
			$this->persistCreate($em, $object);
			$em->flush();

			if (null !== $request->get('btn_save_and_add')) {
				return $this->redirect($this->generateUrl($this->getRoute('create')));
			}
			else {
				return $this->redirect($this->generateUrl($this->getRoute('list')));
			}
		}

		return $this->render('SfsAdminBundle:CRUD:create.html.twig', array(
				'form'				=> $form->createView(),
				'object' 			=> $object
		));
	}

	public function readAction($id) {
		return $this->redirect($this->generateUrl($this->getRoute('update'), array('id' => $id)));
	}

	// Persist function called when sending an update form
	public function persistUpdate($em, $object) {
		$em->persist($object);
	}
	public function updateAction($id, Request $request) {
		$em = $this->container->get('doctrine')->getManager();
		$repository = $em->getRepository($this->entityClass);
		$object = $repository->findOneById($id);
		$this->parseAssociations($object);

		if($object === null)
			throw new NotFoundHttpException("Can't find the object with the id ". $id ." to edit");

		$form = $this->setUpdateForm($object);

		$form->handleRequest($request);
		if ($form->isValid()) {

			$this->persistAssociations($em, $object);

			$this->persistUpdate($em, $object);
			$em->flush();

	        if (null !== $request->get('btn_save_and_list')) {
				return $this->redirect($this->generateUrl($this->getRoute('list')));
	        }
		}

		return $this->render('SfsAdminBundle:CRUD:update.html.twig', array(
				'form'				=> $form->createView(),
				'object' 			=> $object
		));
	}

	public function deleteAction($id, Request $request) {
		$em = $this->container->get('doctrine')->getManager();
		$repository = $em->getRepository($this->entityClass);
		$object = $repository->findOneById($id);
		
		if($object === null) {
			throw new NotFoundHttpException("Can't find the object with the id ". $id ." to delete");
		}
		else {
			$form = $this->createForm(new DeleteType());

			$form->handleRequest($request);
			if ($form->isValid()) {
				$em->remove($object);
				$em->flush();

				return $this->redirect($this->generateUrl($this->getRoute('list')));
			}
			else {
				return $this->render('SfsAdminBundle:CRUD:delete.html.twig', array(
						'form'				=> $form->createView(),
						'object' 			=> $object
				));
			}
		}
	}

	public function exportAction($format = 'csv') {
		$entityClass = $this->entityClass;
		$listFields = $this->setListFields();
		$em = $this->container->get('doctrine')->getManager();

		return Exporter::getResponse($format, null, $em, $entityClass, $listFields);
	}

	public function setSlug($slug) {
		$this->slug = $slug;
	}
	public function getSlug() {
		return $this->slug;
	}
	public function setTitle($title) {
		$this->title = $title;
	}
	public function getTitle() {
		return $this->title;
	}

	public function getRoute($action) {
		return $this->getCore()->getRouteBySlug($this->getSlug(), $action);
	}

	public function getCore() {
		return $this->container->get('sfs.admin.core');
	}

	public function getEntityClass() {
		return $this->entityClass;
	}

	private function getMetadata($class)
	{
		$em = $this->container->get('doctrine')->getManager();
		return $em->getMetadataFactory()->getMetadataFor($class);
	}
}
