<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Filter;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\DateTimeRangeFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeRangeFilter extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
				'left_datetime_options' => array(
					'widget' => 'single_text',
					'label' => 'From',
					'attr' => array(
						'data-date-locale' => 'en',
						'data-date-format' => 'YYYY-MM-DD HH:mm:ss'
					)
				),
				'right_datetime_options' => array(
					'widget' => 'single_text',
					'label' => 'To',
					'attr' => array(
						'data-date-locale' => 'en',
						'data-date-format' => 'YYYY-MM-DD HH:mm:ss'
						)
            		),
				'required'               => false,
                'data_extraction_method' => 'default',
            ))
            ->setAllowedValues('data_extraction_method', array('default'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return DateTimeRangeFilterType::class;
    }

	public function getBlockPrefix()
	{
		return 'sfs_admin_filter_datetime_range';
	}

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sfs_admin_filter_datetime_range';
    }
}
