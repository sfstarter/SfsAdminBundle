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
	/**
	 * @var array
	 */
	protected $tabs = array();

	/**
	 * @var array
	 */
	protected $blocks = array();

	/**
	 * @var string
	 */
	protected $currentTab = null;

	/**
	 * @var string
	 */
	protected $currentBlock = null;

	/**
	 * Adds a new tab to the form
	 * 
	 * @param string $name
	 * @throws \RuntimeException
	 * 
	 * @return FormBuilder
	 */
	public function addTab($name) {
		if($this->currentTab)
			throw new \RuntimeException('You must end the tab before creating a new one');

		$key = count($this->tabs) + 1;
		$this->tabs[$key] = $name;
		$this->currentTab = $key;

		$this->getType()->getInnerType()->addTab($key, $name);

		return $this;
	}

	/**
	 * Ends a tab definition
	 * 
	 * @throws \RuntimeException
	 * 
	 * @return FormBuilder
	 */
	public function endTab() {
		if($this->currentTab === null)
			throw new \RuntimeException('You cannot end a tab as no one is opened');
		if($this->currentBlock)
			throw new \RuntimeException('You must end the block before closing a tab');

		$this->currentTab = null;
	
		return $this;
	}

	/**
	 * getCurrentTab
	 * 
	 * @return string
	 */
	public function getCurrentTab() {
		return $this->currentTab;
	}

	/**
	 * Adds a new block to the form
	 * 
	 * @param string $name
	 * @param array $classes
	 * @throws \RuntimeException
	 * 
	 * @return FormBuilder
	 */
	public function addBlock($name, $classes = array()) {
		if(0 < count($this->tabs) && $this->currentTab === null)
			throw new \RuntimeException('You must create a tab before opening a block');
		if($this->currentBlock)
			throw new \RuntimeException('You must end the block before creating a new one');

		$key = count($this->blocks) + 1;
		$this->blocks[$key] = $name;
		$this->currentBlock = $key;

		$this->getType()->getInnerType()->addBlock($key, $name, array(), $classes);
		if(null !== $this->currentTab) {
            $this->getType()->getInnerType()->addBlockToTab($this->currentTab, $this->currentBlock);
        }

		return $this;
	}

	/**
	 * Ends a block definition
	 * 
	 * @throws \RuntimeException
	 * 
	 * @return FormBuilder
	 */
	public function endBlock() {
		if($this->currentBlock === null)
			throw new \RuntimeException('You cannot end a block as no one is opened');

		$this->currentBlock = null;

		return $this;
	}

	/**
	 * getCurrentBlock
	 * 
	 * @return string
	 */
	public function getCurrentBlock() {
		return $this->currentBlock;
	}

    /**
     * {@inheritdoc}
     */
    public function add($child, $type = null, array $options = array()) {
        if(is_subclass_of($this->getType()->getInnerType(), 'Sfs\AdminBundle\Form\AbstractAdminType')) {
            if (0 < count($this->tabs) && $this->currentTab === null)
                throw new \RuntimeException('You must create a tab before adding fields');
            if ($this->currentBlock === null)
                throw new \RuntimeException('You must create a block before adding fields');

            $this->getType()->getInnerType()->addFieldToBlock($this->currentBlock, $child);
        }

    	return parent::add($child, $type, $options);
    }
}
