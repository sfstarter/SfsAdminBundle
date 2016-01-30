<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Chart;

abstract class ChartAbstract implements ChartInterface
{
	protected $twig;
	protected $datas = array();

	abstract protected function getTwigFile();

	public function __construct($twig) {
		$this->twig = $twig;
	}

	public function render() {
		$uniqid = uniqid();
		return $this->twig->render($this->getTwigFile(), array(
				'datas' => $this->datas,
				'uniqid' => $uniqid
		));
	}
}
