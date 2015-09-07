<?php

/**
 * Description of Clock
 *
 * @author Lolek
 */
class Clock
{
	public static $months, $monthsFormated, $days, $daysFormated, $today, $yesterday, $tommorow;

	public static $timeZones = array(
		-12 => 'Pacific/Kwajalein', -11 => 'Pacific/Samoa', -10 => 'Pacific/Honolulu', -9.5 => 'Pacific/Marquesas',
		-9 => 'Pacific/Gambier', -8 => 'America/Tijuana', -7 => 'America/Chihuahua', -6 => 'America/Chicago',
		-5 => 'America/New_York', -4.5 => 'America/Caracas', -4 => 'America/Santiago', -3.5 => 'America/St_Johns',
		-3 => 'America/Buenos_Aires', -2 => 'Atlantic/South_Georgia', -1 => 'Atlantic/Cape_Verde',
		0 => 'Europe/London',
		1 => 'Europe/Paris', 2 => 'Europe/Helsinki', 3 => 'Europe/Moscow', 3.5 => 'Asia/Tehran',
		4 => 'Asia/Baku', 4.5 => 'Asia/Kabul', 5 => 'Asia/Karachi', 5.5 => 'Asia/Calcutta',
		5.75 => 'Asia/Katmandu', 6 => 'Asia/Dhaka', 6.5 => 'Asia/Rangoon', 7 => 'Asia/Bangkok',
		8 => 'Asia/Hong_Kong', 9 => 'Asia/Tokyo', 9.5 => 'Australia/Adelaide', 10 => 'Australia/Sydney',
		10.5 => 'Australia/Lord_Howe', 11 => 'Asia/Magadan', 11.5 => 'Pacific/Norfolk', 12 => 'Pacific/Fiji',
		12.75 => 'Pacific/Chatham', 13 => 'Pacific/Tongatapu');

	public static $months = array(
		'January', 'February', 'March', 'April', 'May', 'June',
		'July', 'August', 'September', 'October', 'November', 'December');

	public static $shortMonths = array(
		'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
		'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

	public static $days = array(
		'Monday', 'Tu', 'We', 'Th', 'Friday', 'Saturday', 'Sunday');

	public static $shortDays = array(
		'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su');

	public static $monthsFormatted, $daysFormated = array();
}
?>
