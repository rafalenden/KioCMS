<?php

// KioCMS - Kiofol Content Managment System
// blocks/calendar/calendar.block.php

class Calendar extends Block
{

	public function __construct($attributes = array())
	{
		parent::__construct($attributes);

		Kio::addCssFile('blocks/calendar/calendar.css');
	}

	public function getContent()
	{
		//Lang::load('blocks/calendar/lang.*.php');
		$today = date('j');
		$month = date('n');
		$year = date('Y');

		if ($month < 8 && $month % 2 == 1 || $month > 7 && $month % 2 == 0)
		{
			$total_days = 31;
		}
		else
		{
			$total_days = $month == 2 ? (date('L') ? 29 : 28) : 30;
		}

		$first_day = date('w', mktime(1, 1, 1, $month, 0, $year));
		$last_day = date('w', mktime(1, 1, 1, $month, $total_days - 1, $year));

		if ($first_day != 0)
		{
			$colspan = $first_day;
		}
		if (6 - $last_day != 0)
		{
			$colspan2 = 6 - $last_day;
		}

		$days = null;

		for ($day = 1; $day <= $total_days; ++$day)
		{
			$day_of_week = date('w', mktime(1, 1, 1, $month, $day - 1, $year));

			if ($day == 1 || $day_of_week == 0)
			{
				$days .= '<tr class="border-1-parent" title="'.t('Week: %week', array('%week' => date('W', mktime(1, 1, 1, $month, $day, $year)))).'">';

				if ($colspan > 0 && $day == 1)
				{
					$days .= '<td colspan="'.$colspan.'" class="empty">&nbsp;</td>';
				}
			}

			$days .= '<td><a';

			if ($day == $today)
			{
				$days .= ' class="today border-2"';
			}

			$days .= ' href="#'.$day.'.'.$month.'.'.$year.'">'.$day.'</a></td>';

			if ($day == $total_days && $colspan2 > 0)
			{
				$days .= '<td colspan="'.$colspan2.'" class="empty">&nbsp;</td>';
			}

			if ($day_of_week == 6 || $day == $total_days)
			{
				$days .= '</tr>';
			}
		}

		try
		{
			$tpl = new PHPTAL('blocks/calendar/month_view.html');
			$tpl->days = $days;
			$tpl->month_year = date('m').'/'.$year;
			return $tpl->execute();
		}
		catch (Exception $e)
		{
			return template_error($e->getMessage());
		}
	}
}
