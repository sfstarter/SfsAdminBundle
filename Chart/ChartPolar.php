<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Chart;

class ChartPolar extends PieAbstract
{
	protected function getTwigFile() {
		return '@SfsAdmin/Chart/polar.html.twig';
	}
}
