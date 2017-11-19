<?php

/**
 * SfsAdminBundle - Symfony2 project
 * 
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Controller;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

use Sfs\AdminBundle\Exporter\Exporter;
use Sfs\AdminBundle\Form\AbstractFilterType;
use Sfs\AdminBundle\Form\BatchType;
use Sfs\AdminBundle\Form\DeleteType;
use Sfs\AdminBundle\Form\ExportType;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;

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
	 * Array of templates for the CRUD of the current admin. Contains the defaults paths, to be override
	 *
	 * @var array
	 */
	protected $templates = array(
		'list'		                => 'SfsAdminBundle:CRUD:list.html.twig',
		'embedded_relation_list'	=> 'SfsAdminBundle:CRUD:embedded_relation_list.html.twig',
		'create'	                => 'SfsAdminBundle:CRUD:create.html.twig',
		'create_ajax'	            => 'SfsAdminBundle:CRUD:create_ajax.html.twig',
		'update'	                => 'SfsAdminBundle:CRUD:update.html.twig',
		'update_ajax'	            => 'SfsAdminBundle:CRUD:update_ajax.html.twig',
		'delete'	                => 'SfsAdminBundle:CRUD:delete.html.twig',
		'delete_ajax'	            => 'SfsAdminBundle:CRUD:delete_ajax.html.twig',
		'delete_relation_ajax'      => 'SfsAdminBundle:CRUD:delete_relation_ajax.html.twig',
		'batch'		                => 'SfsAdminBundle:CRUD:batch.html.twig'
	);

	/**
	 * Array of accessible actions for current admin: only the routes inside this array will be configured & generated
	 *
	 * @var array
	 */
	protected $actions = array(
		'list',
		'list_ajax',
		'add_relation',
		'embedded_relation_list',
		'create',
		'update',
		'delete',
		'delete_relation',
		'export',
		'batch'
	);

	/**
	 * Array of batch actions applied on list view. By default only delete is implemented
	 * The key is directly related to the name of the sf2 action : batch{Key}
	 *
	 *
	 * @var array
	 */
	protected $batchActions = array(
		'delete'
	);

	/**
	 * Set the form to be displayed on update view
	 * 
	 * @param Form $object
	 */
	abstract protected function setUpdateForm($object);

	/**
	 * @param string
	 */
	public function __construct($entityClass) {
		$this->entityClass = $entityClass;

		$this->setTemplates();
		$this->setActions();
		$this->setBatchActions();
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
		$form = $this->container->get('sfs_admin.form.factory')->create($type, $data, $options);

		return $form;
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
        /** @var QueryBuilder $query */
		$query = $em->getRepository($this->entityClass)->createQueryBuilder('object');

		// Filter form
		if($this->filterForm !== null) {
			$this->filterForm->handleRequest($request);
			// build the query filter
			if ($this->filterForm->isValid()) {
				/** @var \Doctrine\ORM\QueryBuilder $query */
				$query = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($this->filterForm, $query);
			}

			$viewFilterForm = $this->filterForm->createView();
		}
		else
			$viewFilterForm = null;

		// Export form
		$exportForm = $this->createForm(ExportType::class, null, array(
				'action' => $this->generateUrl($this->getRoute('export')),
				'fields' => $this->getObjectProperties()
		));
		$viewExportForm = $exportForm->createView();

		// Pagination & sort mechanism
		$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
				$query, /* query applied */
				$request->query->getInt('page', 1)/* page number */,
				10,/* limit per page */
				array('defaultSortFieldName' => 'object.'. $this->getIdentifierProperty(), 'defaultSortDirection' => 'asc') /* Default sort */
		);
		$pagination->setPageRange(4);

		$listFields = $this->setListFields();
		$batchActions = $this->getBatchActions();

		return $this->render($this->getTemplate('list'), array(
				'filterForm' => $viewFilterForm,
				'exportForm' => $viewExportForm,
				'batchActions' => $batchActions,
				'listFields' => $listFields,
				'pagination' => $pagination
		));
	}

    /**
     * The returned format is given by select2 documentation
     * In case another plugin is used, this action should probably need to be overridden
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listAjaxAction(Request $request) {
        $em = $this->container->get('doctrine')->getManager();
        /** @var \Doctrine\ORM\QueryBuilder $query */
        $query = $em->getRepository($this->entityClass)->createQueryBuilder('object');

        $search = $request->get('term');
        $page = $request->get('page', 1);
        $elementsPerPage = 10;

        $results = $this->queryAjax($query, $search, $page, $elementsPerPage);

        // Pagination calculation
        {
            $query->select('count(object)')
            ->setFirstResult(0);
            $countResult = $query->getQuery()->getSingleScalarResult();
            $isPagination = ((($page - 1) * $elementsPerPage) < $countResult) ? true : false;
        }

        $list = array_map(function($element) {
            return array(
                'id' => $element->getId(),
                'text' => $element->__toString()
            );
        }, $results);

        return new JsonResponse(array(
            "results" => $list,
            "pagination" => array("more" => $isPagination)
        ));
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param string $search
     * @param int $page
     * @param int $elementsPerPage
     * @return array
     */
    protected function queryAjax($query, $search, $page, $elementsPerPage) {
        if($search !== null) {
            $query
                ->where('object.' . $this->getIdentifierProperty() . ' = :search')
                ->setMaxResults($elementsPerPage)
                ->setFirstResult(($page - 1) * $elementsPerPage)
                ->orderBy('object.'. $this->getIdentifierProperty())
                ->setParameter('search', $search);
        }
        // The queryBuilder does not handle a 'where FALSE' so we fake it
        else
            $query->where('object.id = FALSE');

        $results = $query->getQuery()->getSQL()->getResult();

        return $results;
    }

    /**
     * Called on TableEntityType to display the list, with elements filtered for the current element
     * This action is very similar to the listAction,
     *      but we keep it separated to do specific logic here later
     *
     * @param string $property
     * @param int $relationId
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function embeddedRelationListAction($property, $relationId) {
	    $request = $this->get('request_stack')->getMasterRequest();
        // TODO : check here if the object has the property, to avoid crashing
        $accessor = PropertyAccess::createPropertyAccessor();
        if(!isset($this->getMetadata($this->getEntityClass())->getAssociationMappings()[$property])) {
            throw new NoSuchPropertyException("The current object ". $this->getEntityClass() ." has no property named ". $property);
        }

        $em = $this->container->get('doctrine')->getManager();
        /** @var QueryBuilder $query */
        $query = $em->getRepository($this->entityClass)->createQueryBuilder('object');

        // relationId is false on create pages, so we have to handle this case
        if($relationId !== null)
            $query->where('object.'. $property .'= '. $relationId);
        // The queryBuilder does not handle a 'where FALSE' so we fake it
        else
            $query->where('object.id = FALSE');

        // Filter form is disabled, for now at least
        $viewFilterForm = null;

        // Pagination & sort mechanism
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query applied */
            $request->query->getInt($property .'Page', 1)/* page number */,
            10,/* limit per page */
            array(
                'sortFieldParameterName' => $property .'Sort',
                'sortDirectionParameterName' => $property .'Direction',
                'pageParameterName' => $property .'Page',
                'defaultSortFieldName' => 'object.'. $this->getIdentifierProperty(),
                'defaultSortDirection' => 'asc') /* Default sort */
        );
        $pagination->setPageRange(3);

        $listFields = $this->setListFields();
        // Remove the current object as it is unnecessary since they all relate to it
        $listFields = array_filter($listFields, function($key) use ($property) {
            // If the property is exactly the same as the key (meaning the __toString is used)
            if($key === $property) {
                return false;
            }
            // Otherwise remove the column if it has the pattern "property.var"
            else {
                return !(strpos($key, $property .'.') === 0);
            }
        }, ARRAY_FILTER_USE_KEY);

        return $this->render($this->getTemplate('embedded_relation_list'), array(
            'listFields' => $listFields,
            'relationProperty' => $property,
            'entityClass' => $this->getEntityClass(),
            'relationId' => $relationId,
            'pagination' => $pagination,
            'isNullable' => $this->isPropertyNullable($property)
        ));
    }

    public function addRelationAction($id, $property, $relationId, Request $request) {
        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository($this->entityClass);
        $object = $repository->find($id);

        $relationClass = $this->getMetadata($this->getEntityClass())->getAssociationMappings()[$property]['targetEntity'];
        $repository = $em->getRepository($relationClass);
        $relationObject = $repository->find($relationId);

        // Check wether one of the objects doesn't exist
        if($object === null) {
            throw new NotFoundHttpException("Can't find the object with the identifier ". $id);
        }
        if($relationObject === null) {
            throw new NotFoundHttpException("Can't find the object with the identifier ". $relationId);
        }

        // Check if the relation already exist & no update required
        $accessor = PropertyAccess::createPropertyAccessor();
        if($accessor->getValue($object, $property) === $relationObject) {
            $this->addFlash(
                'error',
                $this->get('translator')->trans('sfs.admin.message.embedded_relation.add_existing', array(
                    '%target%' => $object->__toString(),
                ))
            );
        }
        // If everything ok, let's set the value
        else if ($accessor->isWritable($object, $property)) {
            $accessor->setValue($object, $property, $relationObject);

            $em->persist($object);
            $em->flush();

            $this->addFlash(
                'success',
                $this->get('translator')->trans('sfs.admin.message.embedded_relation.add_success', array(
                    '%current%' => $object->__toString(),
                    '%target%' => $relationObject->__toString()
                ))
            );
        }
        else {
            $this->addFlash(
                'error',
                $this->get('translator')->trans('sfs.admin.message.embedded_relation.add_error', array(
                    '%target%' => $object->__toString(),
                ))
            );
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param int $id
     * @param string $property
     * @param int $relationId (not used for now, but might be useful later)
     * @param Request $request
     * @return Response
     */
    public function deleteRelationAction($id, $property, $relationId, Request $request) {
        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository($this->entityClass);
        $object = $repository->find($id);

        if($object === null) {
            throw new NotFoundHttpException("Can't find the object with the identifier ". $id ." to delete");
        }

        // To delete a relation, let's use the same form as for a hard delete
        $form = $this->createForm(DeleteType::class, null, array(
            'action' => $this->generateUrl($this->getRoute('delete_relation'), array(
                'id' => $id,
                'property' => $property,
                'relationId' => $relationId
            ))
        ));

        $form->handleRequest($request);
        // Compute the form if valid, otherwise just (re)send the template
        if ($form->isValid()) {

            $accessor = PropertyAccess::createPropertyAccessor();
            if ($accessor->isWritable($object, $property)) {
                // Check if the relation can be nullable, otherwise we can't remove it
                if ($this->isPropertyNullable($property)) {
                    $accessor->setValue($object, $property, null);

                    $em->persist($object);
                    $em->flush();

                    $this->addFlash(
                        'success',
                        $this->get('translator')->trans('sfs.admin.message.embedded_relation.remove_success', array(
                            '%id%' => $object->getId(),
                            '%name%' => $object->__toString()
                        ))
                    );
                }
                else {
                    $this->addFlash(
                        'error',
                        $this->get('translator')->trans('sfs.admin.message.embedded_relation.remove_error', array(
                            '%id%' => $object->getId(),
                            '%name%' => $object->__toString()
                        ))
                    );
                }
            }
            else {
                $this->addFlash(
                    'error',
                    $this->get('translator')->trans('sfs.admin.message.embedded_relation.remove_error', array(
                        '%id%' => $object->getId(),
                        '%name%' => $object->__toString()
                    ))
                );
            }

            return $this->redirect($request->headers->get('referer'));
        }
        // This case handles CSRF errors & co
        else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash(
                'error',
                $this->get('translator')->trans('sfs.admin.message.embedded_relation.remove_error', array(
                    '%id%' => $object->getId(),
                    '%name%' => $object->__toString()
                ))
            );
        }

        return $this->render($this->getTemplate('delete_relation_ajax'), array(
            'form' => $form->createView(),
            'object' => $object,
            'property' => $property
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
	protected function parseAssociations($object) {
		if(!is_object($object)) {
			return;
		}

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

		/** @var \Symfony\Component\Form\Form $form */
		$form = $this->setCreateForm($object);

		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->container->get('doctrine')->getManager();

			$this->persistAssociations($em, $object);
			$this->persistCreate($em, $object);
			$em->flush();

            $this->addFlash(
                'success',
                $this->get('translator')->trans('sfs.admin.message.create_success', array(
                    '%id%' => $object->getId(),
                    '%name%' => $object->__toString()
                ))
            );

            // Ajax call
            if($request->isXmlHttpRequest()) {
                return new JsonResponse(array(
                    'result' => 'success',
                    'message' => 'Created'
                ));
            }
            else {
                if (null !== $request->get('btn_save_and_add')) {
                    return $this->redirect($this->generateUrl($this->getRoute('create')));
                } else {
                    return $this->redirect($this->generateUrl($this->getRoute('list')));
                }
            }
		}
		else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash(
                'error',
                $this->get('translator')->trans('sfs.admin.message.create_error', array())
            );
        }

        if($request->isXmlHttpRequest()) {
            return $this->render($this->getTemplate('create_ajax'), array(
                'form' => $form->createView(),
                'object' => $object
            ));
        }
        else {
            return $this->render($this->getTemplate('create'), array(
                'form'				=> $form->createView(),
                'object' 			=> $object
            ));
        }
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
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function updateAction($id, Request $request) {
		$em = $this->container->get('doctrine')->getManager();
		$repository = $em->getRepository($this->entityClass);
		$object = $repository->find($id);

		if($object === null)
			throw new NotFoundHttpException("Can't find the object with the identifier ". $id ." to edit");

		$this->parseAssociations($object);

		$form = $this->setUpdateForm($object);

		$form->handleRequest($request);
		if ($form->isValid()) {

			$this->persistAssociations($em, $object);

			$this->persistUpdate($em, $object);
			$em->flush();

            $this->addFlash(
                'success',
                $this->get('translator')->trans('sfs.admin.message.update_success', array(
                    '%id%' => $object->getId(),
                    '%name%' => $object->__toString()
                ))
            );

            // Ajax call
            if($request->isXmlHttpRequest()) {
                return new JsonResponse(array(
                    'result' => 'success',
                    'message' => 'Updated'
                ));
            }
            else {
                if (null !== $request->get('btn_save_and_list')) {
                    return $this->redirect($this->generateUrl($this->getRoute('list')));
                }
            }
		}
		else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash(
                'error',
                $this->get('translator')->trans('sfs.admin.message.update_error', array(
                    '%id%' => $object->getId(),
                    '%name%' => $object->__toString()
                ))
            );
        }

        if($request->isXmlHttpRequest()) {
            return $this->render($this->getTemplate('update_ajax'), array(
                'form' => $form->createView(),
                'object' => $object
            ));
        }
        else {
            return $this->render($this->getTemplate('update'), array(
                'form' => $form->createView(),
                'object' => $object
            ));
        }
	}

	/**
	 * Action called to display the warning before final deletion. It generates a form so that the delete doesn't rely on a url
	 * 
	 * @param integer $id
	 * @param Request $request
	 * @throws NotFoundHttpException
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function deleteAction($id, Request $request) {
		$em = $this->container->get('doctrine')->getManager();
		$repository = $em->getRepository($this->entityClass);
		$object = $repository->find($id);
		
		if($object === null) {
			throw new NotFoundHttpException("Can't find the object with the identifier ". $id ." to delete");
		}
		else {
			$form = $this->createForm(DeleteType::class, null, array(
                'action' => $this->generateUrl($this->getRoute('delete'), array('id' => $id))
            ));

			$form->handleRequest($request);
			if ($form->isValid()) {
				$em->remove($object);
				$em->flush();

                $this->addFlash(
                    'success',
                    $this->get('translator')->trans('sfs.admin.message.delete_success', array(
                        '%id%' => $object->getId(),
                        '%name%' => $object->__toString()
                    ))
                );

                // Ajax calls
                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'result' => 'success',
                        'message' => 'Deleted'
                    ));
                }
                else {
                    return $this->redirect($this->generateUrl($this->getRoute('list')));
                }
            }
			else if($form->isSubmitted() && !$form->isValid()) {
                $this->addFlash(
                    'error',
                    $this->get('translator')->trans('sfs.admin.message.delete_error', array(
                        '%id%' => $object->getId(),
                        '%name%' => $object->__toString()
                    ))
                );
            }

            // Ajax calls
            if($request->isXmlHttpRequest()) {
                return $this->render($this->getTemplate('delete_ajax'), array(
                    'form' => $form->createView(),
                    'object' => $object
                ));
            }
            else {
                return $this->render($this->getTemplate('delete'), array(
                    'form' => $form->createView(),
                    'object' => $object
                ));
            }
		}
	}

	/**
 	 * Call the exporter to return a streamed response with the file, using the form specifying which fields to export
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function exportAction(Request $request) {
		// Export form
		$exportForm = $this->createForm(ExportType::class, null, array(
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
	 * Receives ids to be manipulated & action from hand written form, from listAction(no CSRF test)
	 * A BatchType (with CSRF) is filled with those values, and have to be confirmed to do activate the batchAction
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function batchAction(Request $request) {
		$form = $this->createForm(BatchType::class);

		$form->handleRequest($request);

		// If form is valid, then activate the batch action
		if ($form->isValid()) {
			$ids = json_decode($form->get('batch_ids')->getData());
			// Check if the method 'batch'.Action is available
			$batchMethod = 'batch'. ucfirst($form->get('batch_action')->getData());
			if(method_exists($this, $batchMethod) && count($ids)) {
				$this->{$batchMethod}($ids);
			}

			return $this->redirect($this->generateUrl($this->getRoute('list')));
		}
		// Otherwise fill the fields with POST values from listAction
		else {
			$ids = json_encode($request->request->get('ids'));
			$batchAction = $request->request->get('action');

			// If no selection, automatically redirect to listing
			if(count($request->request->get('ids')) == 0) {
				return $this->redirect($this->generateUrl($this->getRoute('list')));
			}

			/**
			 * Set in hidden fields the type of batch & the ids to be manipulated,
			 * so that we keep them in the next action
			 */
			$form->get('batch_ids')->setData($ids);
			$form->get('batch_action')->setData($batchAction);
			return $this->render($this->getTemplate('batch'), array(
				'form' => $form->createView(),
				'batchAction' => $batchAction
			));
		}
	}

	/**
	 * Only called once the user confirmed the batch deletion.
	 * TODO: The query is inside the Ctrl, should we have a modelRepository with a batchDelete method or something ?
	 *
	 * @param array $ids It is the array of ids to be deleted, for the current entity
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	protected function batchDelete($ids) {
		$em = $this->container->get('doctrine')->getManager();

		/**
		 * @var  QueryBuilder $qb
		 */
		$qb = $em->createQueryBuilder();
		$qb
			->delete($this->getEntityClass(), 'o')
			->where(
				$qb->expr()->in('o.'. $this->getIdentifierProperty(), ':ids')
			)
			->setParameter('ids', $ids)
		;

		// We could/should do some tests on ids array size and effective number of deletion
		$numDeletion = $qb->getQuery()->execute();

		if($numDeletion > 0) {
            $this->addFlash(
                'success',
                $this->get('translator')->trans('sfs.admin.message.batch.delete_success', array())
            );
        }
        else {
            $this->addFlash(
                'error',
                $this->get('translator')->trans('sfs.admin.message.batch.delete_error', array())
            );
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
	 * @return \Sfs\AdminBundle\Core\CoreAdmin
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
		if($class !== null) {
			/**
			 * @var EntityManager $em
			 */
			$em = $this->container->get('doctrine')->getManager();

			return $em->getMetadataFactory()->getMetadataFor($class);
		}
		else {
			return null;
		}
	}

	/**
	 * Get all properties of the current entity
	 *
	 * @return array
	 */
	private function getObjectProperties() {
		$fields = array();
		$metadatas = $this->getMetadata($this->entityClass);

		// Fields
		foreach($metadatas->fieldMappings as $field) {
			$fields[$field['fieldName']] = array('name' => $field['fieldName'], 'fieldType' => $field['type']);
		}

		// Associations are merged to get a complete object
		$associations = $metadatas->getAssociationMappings();
		foreach($associations as $association) {
			$fields[$association['fieldName']] = array('name' => $association['fieldName'], 'fieldType' => 'object');
		}

		return $fields;
	}

    /**
     * @param string $property
     * @return bool
     */
    private function isPropertyNullable($property) {
	    $metadatas = $this->getMetadata($this->entityClass);
        return $metadatas->getAssociationMapping($property)['joinColumns']['0']['nullable'];
    }

	/**
	 * Useful to get the identifier to use, and not directly the id property which may doesn't exist.
	 * No need to throw an exception as Doctrine does it for us
	 *
	 * @return string
	 * @throws \Doctrine\ORM\Mapping\MappingException
	 */
	public function getIdentifierProperty() {
		$property = $this->getMetadata($this->getEntityClass())->getSingleIdentifierFieldName();

		return $property;
	}

	/**
	 * @return array
	 */
	public function getTemplates() {
		return $this->templates;
	}

	/**
	 * @param $slug
	 * @return string
	 */
	public function getTemplate($slug) {
		return $this->templates[$slug];
	}

	/**
	 *
	 * @param $slug
	 * @param $twigPath
	 * @return $this
	 */
	public function setTemplate($slug, $twigPath) {
		$this->templates[$slug] = $twigPath;

		return $this;
	}

	/**
	 * Allows to set & override specific CRUD templates for one admin.
	 * If it's the main view of an action, the index parameter should corresponds to the slug of action, to keep code clean
	 * Called in the construct
	 *
	 * @return $this
	 */
	protected function setTemplates()
	{
		return $this;
	}

	/**
	 * Allows to set & override batchActions for one admin.
	 *
	 * @return AdminController
	 */
	public function setBatchActions()
	{
		return $this;
	}

	/**
	 * @return array
	 */
	public function getBatchActions()
	{
		return $this->batchActions;
	}

	/**
	 * Allows to set & override generated routes & actions for one admin.
	 *
	 * @return AdminController
	 */
	public function setActions()
	{
		return $this;
	}

	/**
	 * @return array
	 */
	public function getActions()
	{
		return $this->actions;
	}
}
