<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class BatchType extends AbstractType
{
	/**
	 * buildForm
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('batch_action', HiddenType::class)
			->add('batch_ids', HiddenType::class)
			->add('confirm', SubmitType::class, array(
				'attr' => array(
					'class'	=> 'btn btn-danger'
				),
				'icon' => 'alert'
			));
	}

	/**
	 * getName
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'admin_batch';
	}
}
