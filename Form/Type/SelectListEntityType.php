<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;


class SelectListEntityType extends SelectListType {
	public function getParent()
	{
		return 'entity';
	}

	public function getName() {
		return 'sfs_admin_field_select_list_entity';
	}
}
