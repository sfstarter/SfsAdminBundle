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
use Symfony\Component\Form\ButtonTypeInterface;
use Symfony\Component\Form\ButtonBuilder;
use Symfony\Component\Form\SubmitButtonTypeInterface;
use Symfony\Component\Form\SubmitButtonBuilder;

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
        if ($this->getInnerType() instanceof ButtonTypeInterface) {
            return new ButtonBuilder($name, $options);
        }

        if ($this->getInnerType() instanceof SubmitButtonTypeInterface) {
            return new SubmitButtonBuilder($name, $options);
        }

		return new FormBuilder($name, $dataClass, new EventDispatcher(), $factory, $options);
	}
}
