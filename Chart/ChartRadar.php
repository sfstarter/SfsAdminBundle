<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Chart;

class ChartRadar extends GraphAbstract
{
	protected function getTwigFile() {
		return 'SfsAdminBundle:Chart:radar.html.twig';
	}
}
