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
use Sfs\AdminBundle\Form\AbstractFilterType;
use Sfs\AdminBundle\Form\ExportType;
use Sfs\AdminBundle\Form\DeleteType;

abstract class AdminController extends Controller
{
	/**
	 * The slug used to identify the admin resource, plus it serves to generate the url
	 * 
	 * @var string
	 */
	protected $slug;

	/**
	 * Title related to the admin resource
	 * 
	 * @var string
	 */
	protected $title;

	/**
	 * Class of the related entity, in string type
	 * 
	 * @var string
	 */
	protected $entityClass;

	/**
	 * The formType for filters
	 * 
	 * @var AbstractFilterType
	 */
	protected $filterForm;

	/**
	 * Used to keep a track of relations and persist them (bonus point for whom finds a better fix)
	 * 
	 * @var array
	 */
	private $associations;

	/**
	 * Used to keep a track of relations and persist them (bonus point for whom finds a better fix)
	 * 
	 * @var array
	 */
	private $relations;

	/**
	 * Set the form to be displayed on update view
	 * 
	 * @param mixed $object
	 */
	abstract protected function setUpdateForm($object);

	/**
	 * @param string
	 */
	public function __construct($entityClass) {
		$this->entityClass = $entityClass;
	}

	/**
	 * Creates and returns a Form instance from the type of the form (override of the Symfony default)
	 *
	 * @param string|\Symfony\Component\Form\FormTypeInterface $type    The built type of the form
	 * @param mixed                    $data    The initial data for the form
	 * @param array                    $options Options for the form
	 *
	 * @return \Symfony\Component\Form\Form
	 */
	protected function createAdminForm($type, $data = null, array $options = array())
	{
		return $this->container->get('sfs_admin.form.factory')->create($type, $data, $options);
	}

	/**
	 * Sets the fields to be listed
	 * Default list array is resumed by it's ID and the __toString value
	 * 
	 * @return array
	 */ 
	protected function setListFields() {
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
	
	/**
	 * Action called to list the entries
	 * 
	 * @param Request $request
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
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

		// Export form
		$exportForm = $this->createForm(new ExportType(), null, array(
				'action' => $this->generateUrl($this->getRoute('export')),
				'fields' => $this->getObjectProperties()
		));
		$viewExportForm = $exportForm->createView();

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
				'exportForm' => $viewExportForm,
				'listFields' => $listFields,
				'pagination' => $pagination
		));
	}

	/**
	 * Set the filter form, but can't be defined automatically so it is set to null by default
	 */
	protected function setFilterForm() {
		$this->filterForm = null;
	}

	/**
	 * Set the form to be displayed on create view
	 * By default the create form is the same as the update one
	 * 
	 * @param mixed $object
	 */
	protected function setCreateForm($object) {
		return $this->setUpdateForm($object);
	}

	/**
	 * Resolves oneToMany relations by keeping them inside an array
	 * 
	 * @param mixed $object
	 */ 
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

	/**
	 * Persist associations registered in associations array. Useful for oneToMany relations
	 * 
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param mixed $object
	 */
	protected function persistAssociations($em, $object) {
		if ($this->associations) {
			foreach ($this->associations as $field => $mapping) {
				if ($mapping['isOwningSide'] === false) {
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

	/**
	 * Persist function called when sending an update form
	 * 
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param mixed $object
	 */
	protected function persistCreate($em, $object) {
		$this->persistUpdate($em, $object);
	}

	/**
	 * Action called for the create form view
	 * 
	 * @param Request $request
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
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

	/**
	 * Action called to view one object. By default it redirects to the update view
	 * 
	 * @param integer $id
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function readAction($id) {
		return $this->redirect($this->generateUrl($this->getRoute('update'), array('id' => $id)));
	}

	/**
	 * Persist function called when sending an update form
	 * 
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param mixed $object
	 */
	protected function persistUpdate($em, $object) {
		$em->persist($object);
	}

	/**
	 * Action called to display the update form
	 * 
	 * @param integer $id
	 * @param Request $request
	 * @throws NotFoundHttpException
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
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

	/**
	 * Action called to display the warning before final deletion. It generates a form so that the delete doesn't rely on a url
	 * 
	 * @param unknown $id
	 * @param Request $request
	 * @throws NotFoundHttpException
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
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

	/**
	 * Call the exporter to return a streamed response with the file, using the form specifying which fields to export
	 * 
	 * @param string $format
	 * 
	 * @return Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function exportAction(Request $request) {
		// Export form
		$exportForm = $this->createForm(new ExportType(), null, array(
				'fields' => $this->getObjectProperties()
		));

		$exportForm->handleRequest($request);
		if ($exportForm->isValid()) {
			$entityClass = $this->entityClass;
			$listFields = $exportForm->getData()['fields'];
			if(!empty($listFields)) {
				$em = $this->container->get('doctrine')->getManager();
				$format = $exportForm->getData()['format'];
	
				return Exporter::getResponse($em, $format, null, $entityClass, $listFields);
			}
		}

		return $this->redirect($this->generateUrl($this->getRoute('list')));
	}

	/**
	 * setSlug
	 * 
	 * @param string $slug
	 * 
	 * @return AdminController
	 */
	public function setSlug($slug) {
		$this->slug = $slug;

		return $this;
	}

	/**
	 * getSlug
	 *
	 * @return string $slug
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * setTitle
	 *
	 * @param string $title
	 *
	 * @return AdminController
	 */
	public function setTitle($title) {
		$this->title = $title;

		return $this;
	}

	/**
	 * getTitle
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Get the route of the specified action, for the current Admin Resource
	 * 
	 * @param string $action
	 * 
	 * @return string $route
	 */
	public function getRoute($action) {
		return $this->getCore()->getRouteBySlug($this->getSlug(), $action);
	}

	/**
	 * Get the core of SfsAdmin
	 * 
	 * @return Sfs\AdminBundle\Core\CoreAdmin
	 */
	public function getCore() {
		return $this->container->get('sfs.admin.core');
	}

	/**
	 * Return the entity class for the current Admin Resource
	 * 
	 * @return string $entityClass;
	 */
	public function getEntityClass() {
		return $this->entityClass;
	}

	/**
	 * Returns a classMetadata (instance that holds all the object-relational mapping metadata) for a specified entity Class
	 * 
	 * @param string $class
	 * 
	 * @return \Doctrine\ORM\Mapping\ClassMetadataInfo
	 */
	private function getMetadata($class)
	{
		$em = $this->container->get('doctrine')->getManager();
		return $em->getMetadataFactory()->getMetadataFor($class);
	}

	/**
	 * Get all properties of the current entity
	 * 
	 * @return array
	 */
	private function getObjectProperties() {
		$fields = array();
		$metadatas = $this->getMetadata($this->entityClass);

		foreach($metadatas->fieldMappings as $field) {
			$fields[] = array('name' => $field['fieldName'], 'fieldType' => $field['type']);
		}

		return $fields;
	}
}
