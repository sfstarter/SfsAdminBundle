<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;


class TagEntityType extends TagType {
	/**
	 * getParent
	 * 
	 * @return string
	 */
	public function getParent()
	{
		return 'entity';
	}

	/**
	 * getName
	 * 
	 * @return string
	 */
	public function getName() {
		return 'sfs_admin_field_tag_entity';
	}
}
