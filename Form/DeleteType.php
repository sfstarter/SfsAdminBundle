<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('delete', 'submit', array(
					'attr' => array(
						'class'	=> 'btn btn-danger'
					),
					'icon' => 'trash'
			));
	}

	public function getName()
	{
		return 'admin_delete';
	}
}
