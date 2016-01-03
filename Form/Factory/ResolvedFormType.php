<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Factory;

use Symfony\Component\Form\ResolvedFormType as BaseResolvedFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ResolvedFormType extends BaseResolvedFormType
{
	/**
	 * Creates a new builder instance.
	 *
	 * Override this method if you want to customize the builder class.
	 *
	 * @param string               $name      The name of the builder.
	 * @param string               $dataClass The data class.
	 * @param FormFactoryInterface $factory   The current form factory.
	 * @param array                $options   The builder options.
	 *
	 * @return FormBuilderInterface The new builder instance.
	 */
	protected function newBuilder($name, $dataClass, FormFactoryInterface $factory, array $options)
	{
		parent::newBuilder($name, $dataClass, $factory, $options);

		return new FormBuilder($name, $dataClass, new EventDispatcher(), $factory, $options);
	}
}
