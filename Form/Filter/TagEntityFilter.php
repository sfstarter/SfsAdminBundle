<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Filter;

use Sfs\AdminBundle\Form\Type\TagEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagEntityFilter extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
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
        return TagEntityType::class;
    }

	public function getBlockPrefix()
	{
		return 'sfs_admin_filter_tag_entity';
	}

	/**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sfs_admin_filter_tag_entity';
    }
}
