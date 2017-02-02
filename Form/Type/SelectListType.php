<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SelectListType extends AbstractType {
	private $defaultAttrOptions = array(
		'class' => 'select-list',
	);

	/**
	 * configureOptions
	 *
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'expanded' => false,
				'multiple' => true,
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
		return ChoiceType::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'sfs_admin_field_select_list';
	}
}
