<?php

namespace Sfs\AdminBundle\Tests\Fixtures\TestBundle\Admin\Form\Type;

use Sfs\AdminBundle\Form\AbstractAdminType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractAdminType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->addTab('Informations')
				->addBlock('Main', array('column' => 'col-md-6'))
					->add('email')
				->endBlock()

				->addBlock('Security', array('column' => 'col-md-6'))
					->add('username')
					->add('plain_password')
					->add('roles')
					->add('enabled')
				->endBlock()
		;
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Sfs\AdminBundle\Tests\Fixtures\TestBundle\Entity\User',
		));
	}

	public function getName()
	{
		return 'admin_user';
	}
}
