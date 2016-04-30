<?php

namespace Sfs\AdminBundle\Tests\Fixtures\TestBundle\Admin;

use Sfs\AdminBundle\Controller\AdminController;

class UserAdmin extends AdminController
{
	protected $resourceName = "User";

	public function setListFields() {
		return array(
				'id' 				=> array('name' => 'ID'),
				'username'			=> array('name' => 'Username'),
				'email'				=> array('name' => 'Email'),
				'enabled'			=> array('name' => 'Enabled')
		);
	}

	protected function setUpdateForm($object) {
		$updateForm = $this->createAdminForm(new Form\Type\UserType(), $object);

		return $updateForm;
	}

	public function persistUpdate($em, $object) {
		$userManager = $this->container->get('fos_user.user_manager');
		$userManager->updateUser($object);
	}

	public function setFilterForm() {
	}
}
