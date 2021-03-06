<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportType extends AbstractType
{
	/**
	 * buildForm
	 * 
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$fields = array();
		foreach($options['fields'] as $key => $f) {
			$fields[$f['name']] = $f['name'];
		}

		$builder->add('fields', ChoiceType::class, array(
			'expanded' => true,
			'multiple' => true,
			'choices' => $fields,
			'choice_attr' => function($val, $key, $index) use ($options) {
				return array(
					'checked' => true,
					'class' => 'batch-row-checkbox',
					'data-field-type' => $options['fields'][$key]['fieldType']
				);
			}
		));

		$builder->add('format', HiddenType::class);
		$builder
			->add('download', SubmitType::class, array(
					'attr' => array(
						'class'	=> 'btn btn-primary'
					),
					'icon' => 'download'
			));
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults (array(
				'fields' => array() 
		));
	}

	/**
	 * getName
	 * 
	 * @return string
	 */
	public function getName()
	{
		return 'admin_export';
	}
}
