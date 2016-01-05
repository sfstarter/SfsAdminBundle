<?php 

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\FormFilterBundle\Event\GetFilterConditionEvent;

use Lexik\Bundle\FormFilterBundle\Event\Subscriber\DoctrineORMSubscriber;

class FilterSubscriber extends DoctrineORMSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
	public static function getSubscribedEvents()
	{
		return array(
			// if a Doctrine\ORM\QueryBuilder is passed to the lexik_form_filter.query_builder_updater service
			'lexik_form_filter.apply.orm.sfs_admin_filter_boolean' => array('filterBoolean'),
			'lexik_form_filter.apply.orm.sfs_admin_filter_datetime_picker' => array('filterDateTime'),
			'lexik_form_filter.apply.orm.sfs_admin_filter_datetime_range' => array('filterDateTimeTextRange'),
			'lexik_form_filter.apply.orm.sfs_admin_filter_select_entity' => array('filterEntity'),
			'lexik_form_filter.apply.orm.sfs_admin_filter_tag_entity' => array('filterEntity'),
		);
	}

	/**
	 * filterDateTimeTextRange
	 * 
	 * @param GetFilterConditionEvent $event
	 */
	public function filterDateTimeTextRange(GetFilterConditionEvent $event)
	{
		$expr   = $event->getFilterQuery()->getExpressionBuilder();
		$values = $event->getValues();
	
		$value = $values['value'];
	
		if (isset($value['left_datetime']) || $value['right_datetime']) {
			$event->setCondition($expr->datetimeInRange($event->getField(), $value['left_datetime'], $value['right_datetime']));
		}
	}
}
