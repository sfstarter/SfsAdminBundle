<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;

class SelectEntityType extends SelectType {
    /**
     * {@inheritdoc}
     */
	public function getParent()
	{
		return 'entity';
	}

    /**
     * {@inheritdoc}
     */
	public function getName() {
		return 'sfs_admin_field_select_entity';
	}
}
