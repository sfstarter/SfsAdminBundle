<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TagEntityType extends TagType {
	/**
	 * getParent
	 * 
	 * @return string
	 */
	public function getParent()
	{
		return EntityType::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'sfs_admin_field_tag_entity';
	}
}
