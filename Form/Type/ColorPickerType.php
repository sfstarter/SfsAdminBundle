<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ColorPickerType extends AbstractType {
	/**
	 * configureOptions
	 *
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
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
		return TextType::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'sfs_admin_field_color_picker';
	}
}
