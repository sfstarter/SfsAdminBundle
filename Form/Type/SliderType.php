<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * For more options and a complete documentation: http://ionden.com/a/plugins/ion.rangeSlider/en.html
 * @author Ramine AGOUNE - SOLID LYNX
 *
 */
class SliderType extends AbstractType {
	/**
	 * @var array
	 */
	private $defaultAttrOptions = array(
		'class' => 'slider',
		'data-step'	=> 1,
		'data-type' => 'single',
		'data-min' => 0,
		'data-max' => 100,
		'data-disable' => false,
		'data-postfix' => ''
	);

	/**
	 * configureOptions
	 *
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
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
		return IntegerType::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'sfs_admin_field_slider';
	}
}
