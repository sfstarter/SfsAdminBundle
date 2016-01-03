<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Form\Factory;

use Symfony\Component\Form\FormBuilder as BaseFormBuilder;

class FormBuilder extends BaseFormBuilder
{
	protected $tabs = array();
	protected $blocks = array();
	protected $currentTab = null;
	protected $currentBlock = null;

	public function addTab($name) {
		if($this->currentTab)
			throw new \RuntimeException('You must end the tab before creating a new one');

		$slug = strtolower(preg_replace('/\s+/', '_', $name));
		$this->tabs[$slug] = $name;
		$this->currentTab = $slug;

		$this->getType()->getInnerType()->addTab($slug, $name);

		return $this;
	}
	public function endTab() {
		if($this->currentTab === null)
			throw new \RuntimeException('You cannot end a tab as no one is opened');
		if($this->currentBlock)
			throw new \RuntimeException('You must end the block before closing a tab');

		$this->currentTab = null;
	
		return $this;
	}
	public function getCurrentTab() {
		return $this->currentTab;
	}

	public function addBlock($name, $classes = array()) {
		if($this->currentTab === null)
			throw new \RuntimeException('You must create a tab before opening a block');
		if($this->currentBlock)
			throw new \RuntimeException('You must end the block before creating a new one');

		$slug = strtolower(preg_replace('/\s+/', '_', $name));
		$this->blocks[$slug] = $name;
		$this->currentBlock = $slug;

		$this->getType()->getInnerType()->addBlock($slug, $name, array(), $classes);
		$this->getType()->getInnerType()->addBlockToTab($this->currentTab, $this->currentBlock);

		return $this;
	}
	public function endBlock() {
		if($this->currentBlock === null)
			throw new \RuntimeException('You cannot end a block as no one is opened');

		$this->currentBlock = null;

		return $this;
	}
	public function getCurrentBlock() {
		return $this->currentBlock;
	}

    public function add($child, $type = null, array $options = array()) {
    	if($this->currentTab === null)
    		throw new \RuntimeException('You must create a tab before adding fields');
		if($this->currentBlock === null)
			throw new \RuntimeException('You must create a block before adding fields');

    	$this->getType()->getInnerType()->addFieldToBlock($this->currentBlock, $child);

    	return parent::add($child, $type, $options);
    }
}
