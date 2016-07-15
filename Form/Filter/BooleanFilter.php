<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Filter;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\BooleanFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BooleanFilter extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
				'expanded' => true,
				'widget_type'  => 'inline',
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
        return BooleanFilterType::class;
    }

	public function getBlockPrefix()
	{
		return 'sfs_admin_filter_boolean';
	}

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sfs_admin_filter_boolean';
    }
}
