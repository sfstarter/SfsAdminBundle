<?php
/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Type;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TableEntityType
 *
 * TableEntity is a "fake" formType, since it will only call a render(controller())
 * The relations created/deleted are managed by http call on distinct url
 *
 * @package Sfs\AdminBundle\Form\Type
 */
class TableEntityType extends AbstractType
{
    private $defaultAttrOptions = array(
    );

    /**
     * configureOptions
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr' => $this->defaultAttrOptions,
            'ajax_route' => null,
			'mapped' => false
        ));

        $resolver->setRequired('class');
        $resolver->setRequired('property');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['ajax_route'] = $options['ajax_route'];
        $view->vars['attr'] = array_merge($this->defaultAttrOptions, $options['attr']);
        $view->vars['class'] = $options['class'];
        $view->vars['property'] = $options['property'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'sfs_admin_field_table_entity';
    }
}