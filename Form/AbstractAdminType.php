<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

abstract class AbstractAdminType extends AbstractType
{
	/**
	 * Registers titles of tabs & the slugs of the blocks they contain
	 * 
	 * @var array
	 */
	protected $tabs = array();

	/**
	 * Registers titles of blocks & the slugs of the fields they contain
	 * 
	 * @var array
	 */
	protected $blocks = array();

	/**
	 * {@inheritdoc}
	 */
    public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['tabs'] = $this->getTabs();
		$view->vars['blocks'] = $this->getBlocks();
	}

	/**
	 * Adds a visual tab to the form
	 * 
	 * @param string $slug
	 * @param string $title
	 * @param array $blocks
	 */
	public function addTab($slug, $title, $blocks = array()) {
		$this->tabs[$slug] = array(
				'title'		=> $title,
				'blocks'	=> $blocks
		);
	}

	/**
	 * Get all the tabs of the form
	 * 
	 * @return array $tabs
	 */
	public function getTabs() {
		return $this->tabs;
	}

	/**
	 * Add a block to the specified tab
	 * 
	 * @param string $tabSlug
	 * @param string $blockSlug
	 */
	public function addBlockToTab($tabSlug, $blockSlug) {
		$this->tabs[$tabSlug]['blocks'][] = $blockSlug;
	}

	/**
	 * Add a visual block to the form
	 * 
	 * @param string $slug
	 * @param string $title
	 * @param array $fields
	 * @param array $classes
	 */
	public function addBlock($slug, $title, $fields = array(), $classes = array()) {
		$this->blocks[$slug] = array(
				'title'		=> $title,
				'classes'	=> $classes,
				'fields'	=> $fields
		);
	}

	/**
	 * Get all the blocks of the form
	 * 
	 * @return array
	 */
	public function getBlocks() {
		return $this->blocks;
	}

	/**
	 * Add a field to the specified block
	 * 
	 * @param string $blockSlug
	 * @param string $fieldName
	 */
	public function addFieldToBlock($blockSlug, $fieldName) {
		$this->blocks[$blockSlug]['fields'][] = $fieldName;
	}
}
