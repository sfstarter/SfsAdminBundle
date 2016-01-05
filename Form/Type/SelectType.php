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


class SelectType extends AbstractType {
	/**
	 * @var array
	 */
	private $defaultAttrOptions = array(
		'class' => 'selectpicker show-tick',
		'data-style' => 'btn-white',
		'data-live-search' => false
	);

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'expanded' => false,
				'multiple' => false,
				'attr' => $this->defaultAttrOptions
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		parent::buildView($view, $form, $options);
		$view->vars['attr'] = array_merge($this->defaultAttrOptions, $options['attr']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return 'choice';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'sfs_admin_field_select';
	}
}
