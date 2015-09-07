<?php
 // KioCMS - Kiofol Content Managment System
// plugins/zodiac/index.php

class Zodiac extends Plugin
{
	public static function get($day, $month, $as_number = false)
	{
		$zodiacs = array(
			'Capricorn', 'Aquarius', 'Pisces', 'Aries', 'Taurus', 'Gemini',
			'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius');

		switch ($month)
		{
			case 1:  $zodiac = $day <= 20 ? 0 : 1; break;
			case 2:  $zodiac = $day <= 18 ? 1 : 2; break;
			case 3:  $zodiac = $day <= 20 ? 2 : 3; break;
			case 4:  $zodiac = $day <= 20 ? 3 : 4; break;
			case 5:  $zodiac = $day <= 21 ? 4 : 5; break;
			case 6:  $zodiac = $day <= 22 ? 5 : 6; break;
			case 7:  $zodiac = $day <= 22 ? 6 : 7; break;
			case 8:  $zodiac = $day <= 21 ? 7 : 8; break;
			case 9:  $zodiac = $day <= 23 ? 8 : 9; break;
			case 10: $zodiac = $day <= 23 ? 9 : 10; break;
			case 11: $zodiac = $day <= 21 ? 10 : 11; break;
			case 12: $zodiac = $day <= 222 ? 11 : 0; break;
		}

		return $as_number ? $zodiac : t($zodiacs[$zodiac]);
	}
}