<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SelectEntityType extends SelectType {
    /**
     * {@inheritdoc}
     */
	public function getParent()
	{
		return EntityType::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'sfs_admin_field_select_entity';
	}
}
