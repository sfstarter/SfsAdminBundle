<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class DateTimePickerType extends AbstractType {
	/**
	 * setDefaultOptions
	 * 
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'widget' => 'single_text',
			'attr' => array(
				'data-date-locale' => 'en',
				'data-date-format' => 'YYYY-MM-DD HH:mm:ss'
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
		return 'datetime';
	}

	/**
	 * getName
	 *
	 * @return string
	 */
	public function getName() {
		return 'sfs_admin_field_datetime_picker';
	}
}
