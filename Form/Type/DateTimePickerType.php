<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DateTimePickerType extends AbstractType {
	/**
	 * configureOptions
	 * 
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
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
		return DateTimeType::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'sfs_admin_field_datetime_picker';
	}
}
