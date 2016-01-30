<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Chart;

abstract class GraphAbstract extends ChartAbstract
{
	public function setLabels($labels) {
		$this->datas['labels'] = $labels;
	}

	public function addRowData($label, $datas, $fillColor = array(0, 0, 0), $strokeColor = array(), $pointColor = array()) {
		if(empty($strokeColor))
			$strokeColor = $fillColor;
		if(empty($pointColor))
			$pointColor = $fillColor;

		$row = array(
			'label' => $label,
			'fillColor' => 'rgba('. $fillColor[0] .', '. $fillColor[1] .', '. $fillColor[2] .', 0.2)',
			'strokeColor' => 'rgba('. $strokeColor[0] .', '. $strokeColor[1] .', '. $strokeColor[2] .', 0.5)',
			'pointColor' => 'rgba('. $pointColor[0] .', '. $pointColor[1] .', '. $pointColor[2] .', 1)',
			'pointStrokeColor' => "#fff",
			'pointHighlightFill' => "#fff",
			'pointHighlightStroke' => 'rgba('. $pointColor[0] .', '. $pointColor[1] .', '. $pointColor[2] .', 1)',
			'data' => $datas
		);

		$this->datas['datasets'][] = $row;
	}

	public function render() {
		if(!isset($this->datas['labels'])) {
			$this->datas['labels'] = array();
		}

		return parent::render();
	}
}
