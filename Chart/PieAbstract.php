<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Chart;

abstract class PieAbstract extends ChartAbstract
{
	public function addRowData($label, $value, $fillColor = array(0, 0, 0), $highlightColor = array()) {
		if(empty($highlightColor))
			$highlightColor = $fillColor;

		$row = array(
			'label' => $label,
			'value' => $value,
			'color' => 'rgba('. $fillColor[0] .', '. $fillColor[1] .', '. $fillColor[2] .', 0.8)',
			'highlight' => 'rgba('. $highlightColor[0] .', '. $highlightColor[1] .', '. $highlightColor[2] .', 1)'
		);

		$this->datas[] = $row;
	}
}
