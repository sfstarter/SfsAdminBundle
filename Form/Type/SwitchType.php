<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class SwitchType extends AbstractType {
	private $defaultSwitchOptions = array(
			'data-on-text' => 'enabled',
			'data-off-text' => 'disabled',
			'data-label-text' => 'content',
			'data-on-color' => 'success',
			'data-off-color' => 'danger'
	);

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'attr' => array(
					'class' => 'switch'
				),
				'switch_colors' => $this->defaultSwitchOptions
		));
	}

	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		parent::buildView($view, $form, $options);

		$view->vars['switch_colors'] = array_merge($this->defaultSwitchOptions, $options['switch_colors']);
	}
	
	public function getParent()
	{
		return 'checkbox';
	}

	public function getName() {
		return 'sfs_admin_field_switch';
	}
}
