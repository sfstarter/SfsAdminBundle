<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SwitchType extends AbstractType {
	/**
	 * @var array
	 */
	private $defaultSwitchOptions = array(
			'data-on-text' => 'enabled',
			'data-off-text' => 'disabled',
			'data-label-text' => 'content',
			'data-on-color' => 'success',
			'data-off-color' => 'danger'
	);

	/**
	 * configureOptions
	 *
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'attr' => array(
					'class' => 'switch'
				),
				'switch_colors' => $this->defaultSwitchOptions
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		parent::buildView($view, $form, $options);

		$view->vars['switch_colors'] = array_merge($this->defaultSwitchOptions, $options['switch_colors']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return CheckboxType::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'sfs_admin_field_switch';
	}
}
