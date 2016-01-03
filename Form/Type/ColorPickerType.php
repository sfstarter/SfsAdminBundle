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
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'attr' => array(
					'class' => 'colorpicker-default',
					'data-color-format' => 'hex'
				)
		));
	}
	
	public function getParent()
	{
		return 'text';
	}

	public function getName() {
		return 'sfs_admin_field_color_picker';
	}
}
