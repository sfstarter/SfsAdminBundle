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
	protected $tabs = array();
	protected $blocks = array();

    public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['tabs'] = $this->getTabs();
		$view->vars['blocks'] = $this->getBlocks();
	}
	
	public function addTab($slug, $title, $blocks = array()) {
		$this->tabs[$slug] = array(
				'title'		=> $title,
				'blocks'	=> $blocks
		);
	}
	public function getTabs() {
		return $this->tabs;
	}
	public function addBlockToTab($tabSlug, $blockSlug) {
		$this->tabs[$tabSlug]['blocks'][] = $blockSlug;
	}

	public function addBlock($slug, $title, $fields = array(), $classes = array()) {
		$this->blocks[$slug] = array(
				'title'		=> $title,
				'classes'	=> $classes,
				'fields'	=> $fields
		);
	}
	public function getBlocks() {
		return $this->blocks;
	}
	public function addFieldToBlock($blockSlug, $fieldName) {
		$this->blocks[$blockSlug]['fields'][] = $fieldName;
	}
}
