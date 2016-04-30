<?php

namespace Sfs\AdminBundle\Tests\Fixtures\TestBundle\Admin;

use Sfs\AdminBundle\Controller\AdminController;

class PageAdmin extends AdminController
{
	protected $resourceName = "Page";

	public function setListFields() {
		return array(
				'id' 				=> array('name' => 'ID'),
				'title'				=> array('name' => 'Title'),
		);
	}

	protected function setUpdateForm($object) {
		$updateForm = $this->createAdminForm(new Form\Type\PageType(), $object);

		return $updateForm;
	}

	public function setFilterForm() {
	}
}
