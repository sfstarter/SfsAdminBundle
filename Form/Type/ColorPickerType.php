<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ColorPickerType extends AbstractType {
	/**
	 * setDefaultOptions
	 * 
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'attr' => array(
					'data-format' => 'hex'
				)
		));
	}

	/**
	 * getParent
	 *
	 * @return string
	 */
	public function getParent()
	{
		return 'text';
	}

	/**
	 * getName
	 *
	 * @return string
	 */
	public function getName() {
		return 'sfs_admin_field_color_picker';
	}
}
