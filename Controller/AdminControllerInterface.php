<?php
/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 * Date: 02/03/18
 * Time: 15:23
 */

namespace Sfs\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

interface AdminControllerInterface
{
    public function __construct($entityClass);
    public function addRelationAction($id, $property, $relationId, Request $request);
    public function batchAction(Request $request);
    public function createAction(Request $request);
    public function deleteAction($id, Request $request);
    public function deleteRelationAction($id, $property, $relationId, Request $request);
    public function embeddedRelationListAction($property, $inversedProperty, $relationId);
    public function exportAction(Request $request);
    public function listAction(Request $request);
    public function listAjaxAction(Request $request);
    public function readAction($id);
    public function updateAction($id, Request $request);

    public function getCore();
    public function getEntityClass();
    public function getEntityClassShortName();
    public function getIdentifierProperty();
    public function getRoute($action);

    public function getActions();
    public function setActions();
    public function getBatchActions();
    public function setBatchActions();
    public function getSlug();
    public function setSlug($slug);
    public function getTemplate($slug);
    public function getTemplates();
    public function setTemplate($slug, $twigPath);
    public function getTitle();
    public function setTitle($title);
}
