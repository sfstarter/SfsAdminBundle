<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Chart;

class ChartDoughnut extends PieAbstract
{
	protected function getTwigFile() {
		return 'SfsAdminBundle:Chart:doughnut.html.twig';
	}
}
