<?php namespace Koldy;

/**
 * Another utility class: the timezones. It is usually PITA, but either way,
 * you'll need to handle it somehow.
 * 
 * @link http://www.urbandictionary.com/define.php?term=pita
 * @TODO needs testing
 */
class Timezone {


	/**
	 * The timezone list defined as "timezone"=>"(GMT) City"
	 * 
	 * @var array
	 * @example "Europe/Zagreb" => "(GMT+01:00) Zagreb"
	 */
	public static $timezones = array(
		'Pacific/Midway' => '(GMT-11:00) Midway Island',
		'US/Samoa' => '(GMT-11:00) Samoa',
		'US/Hawaii' => '(GMT-10:00) Hawaii',
		'US/Alaska' => '(GMT-09:00) Alaska',
		'US/Pacific' => '(GMT-08:00) Pacific Time (US &amp; Canada)',
		'America/Tijuana' => '(GMT-08:00) Tijuana',
		'US/Arizona' => '(GMT-07:00) Arizona',
		'US/Mountain' => '(GMT-07:00) Mountain Time (US &amp; Canada)',
		'America/Chihuahua' => '(GMT-07:00) Chihuahua',
		'America/Mazatlan' => '(GMT-07:00) Mazatlan',
		'America/Mexico_City' => '(GMT-06:00) Mexico City',
		'America/Monterrey' => '(GMT-06:00) Monterrey',
		'Canada/Saskatchewan' => '(GMT-06:00) Saskatchewan',
		'US/Central' => '(GMT-06:00) Central Time (US &amp; Canada)',
		'US/Eastern' => '(GMT-05:00) Eastern Time (US &amp; Canada)',
		'US/East-Indiana' => '(GMT-05:00) Indiana (East)',
		'America/Bogota' => '(GMT-05:00) Bogota',
		'America/Lima' => '(GMT-05:00) Lima',
		'America/Caracas' => '(GMT-04:30) Caracas',
		'Canada/Atlantic' => '(GMT-04:00) Atlantic Time (Canada)',
		'America/La_Paz' => '(GMT-04:00) La Paz',
		'America/Santiago' => '(GMT-04:00) Santiago',
		'Canada/Newfoundland' => '(GMT-03:30) Newfoundland',
		'America/Buenos_Aires' => '(GMT-03:00) Buenos Aires',
		'Greenland' => '(GMT-03:00) Greenland',
		'Atlantic/Stanley' => '(GMT-02:00) Stanley',
		'Atlantic/Azores' => '(GMT-01:00) Azores',
		'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
		'Africa/Casablanca' => '(GMT) Casablanca',
		'Europe/Dublin' => '(GMT) Dublin',
		'Europe/Lisbon' => '(GMT) Lisbon',
		'Europe/London' => '(GMT) London',
		'Africa/Monrovia' => '(GMT) Monrovia',
		'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
		'Europe/Belgrade' => '(GMT+01:00) Belgrade',
		'Europe/Berlin' => '(GMT+01:00) Berlin',
		'Europe/Bratislava' => '(GMT+01:00) Bratislava',
		'Europe/Brussels' => '(GMT+01:00) Brussels',
		'Europe/Budapest' => '(GMT+01:00) Budapest',
		'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
		'Europe/Ljubljana' => '(GMT+01:00) Ljubljana',
		'Europe/Madrid' => '(GMT+01:00) Madrid',
		'Europe/Paris' => '(GMT+01:00) Paris',
		'Europe/Prague' => '(GMT+01:00) Prague',
		'Europe/Rome' => '(GMT+01:00) Rome',
		'Europe/Sarajevo' => '(GMT+01:00) Sarajevo',
		'Europe/Skopje' => '(GMT+01:00) Skopje',
		'Europe/Stockholm' => '(GMT+01:00) Stockholm',
		'Europe/Vienna' => '(GMT+01:00) Vienna',
		'Europe/Warsaw' => '(GMT+01:00) Warsaw',
		'Europe/Zagreb' => '(GMT+01:00) Zagreb',
		'Europe/Athens' => '(GMT+02:00) Athens',
		'Europe/Bucharest' => '(GMT+02:00) Bucharest',
		'Africa/Cairo' => '(GMT+02:00) Cairo',
		'Africa/Harare' => '(GMT+02:00) Harare',
		'Europe/Helsinki' => '(GMT+02:00) Helsinki',
		'Europe/Istanbul' => '(GMT+02:00) Istanbul',
		'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
		'Europe/Kiev' => '(GMT+02:00) Kyiv',
		'Europe/Minsk' => '(GMT+02:00) Minsk',
		'Europe/Riga' => '(GMT+02:00) Riga',
		'Europe/Sofia' => '(GMT+02:00) Sofia',
		'Europe/Tallinn' => '(GMT+02:00) Tallinn',
		'Europe/Vilnius' => '(GMT+02:00) Vilnius',
		'Asia/Baghdad' => '(GMT+03:00) Baghdad',
		'Asia/Kuwait' => '(GMT+03:00) Kuwait',
		'Africa/Nairobi' => '(GMT+03:00) Nairobi',
		'Asia/Riyadh' => '(GMT+03:00) Riyadh',
		'Asia/Tehran' => '(GMT+03:30) Tehran',
		'Europe/Moscow' => '(GMT+04:00) Moscow',
		'Asia/Baku' => '(GMT+04:00) Baku',
		'Europe/Volgograd' => '(GMT+04:00) Volgograd',
		'Asia/Muscat' => '(GMT+04:00) Muscat',
		'Asia/Tbilisi' => '(GMT+04:00) Tbilisi',
		'Asia/Yerevan' => '(GMT+04:00) Yerevan',
		'Asia/Kabul' => '(GMT+04:30) Kabul',
		'Asia/Karachi' => '(GMT+05:00) Karachi',
		'Asia/Tashkent' => '(GMT+05:00) Tashkent',
		'Asia/Kolkata' => '(GMT+05:30) Kolkata',
		'Asia/Kathmandu' => '(GMT+05:45) Kathmandu',
		'Asia/Yekaterinburg'=> '(GMT+06:00) Ekaterinburg',
		'Asia/Almaty' => '(GMT+06:00) Almaty',
		'Asia/Dhaka' => '(GMT+06:00) Dhaka',
		'Asia/Novosibirsk' => '(GMT+07:00) Novosibirsk',
		'Asia/Bangkok' => '(GMT+07:00) Bangkok',
		'Asia/Jakarta' => '(GMT+07:00) Jakarta',
		'Asia/Krasnoyarsk' => '(GMT+08:00) Krasnoyarsk',
		'Asia/Chongqing' => '(GMT+08:00) Chongqing',
		'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
		'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
		'Australia/Perth' => '(GMT+08:00) Perth',
		'Asia/Singapore' => '(GMT+08:00) Singapore',
		'Asia/Taipei' => '(GMT+08:00) Taipei',
		'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaan Bataar',
		'Asia/Urumqi' => '(GMT+08:00) Urumqi',
		'Asia/Irkutsk' => '(GMT+09:00) Irkutsk',
		'Asia/Seoul' => '(GMT+09:00) Seoul',
		'Asia/Tokyo' => '(GMT+09:00) Tokyo',
		'Australia/Adelaide'=> '(GMT+09:30) Adelaide',
		'Australia/Darwin' => '(GMT+09:30) Darwin',
		'Asia/Yakutsk' => '(GMT+10:00) Yakutsk',
		'Australia/Brisbane'=> '(GMT+10:00) Brisbane',
		'Australia/Canberra'=> '(GMT+10:00) Canberra',
		'Pacific/Guam' => '(GMT+10:00) Guam',
		'Australia/Hobart' => '(GMT+10:00) Hobart',
		'Australia/Melbourne' => '(GMT+10:00) Melbourne',
		'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
		'Australia/Sydney' => '(GMT+10:00) Sydney',
		'Asia/Vladivostok' => '(GMT+11:00) Vladivostok',
		'Asia/Magadan' => '(GMT+12:00) Magadan',
		'Pacific/Auckland' => '(GMT+12:00) Auckland',
		'Pacific/Fiji' => '(GMT+12:00) Fiji'
	);


	/**
	 * The timezone offsets in format timezone => offset
	 * 
	 * @var array
	 * @example "Australia/Perth" => 8
	 */
	public static $timezoneOffsets = array(
		'Pacific/Midway' => -11,
		'US/Samoa' => -11,
		'US/Hawaii' => -10,
		'US/Alaska' => -9,
		'US/Pacific' => -8,
		'America/Tijuana' => -8,
		'US/Arizona' => -7,
		'US/Mountain' => -7,
		'America/Chihuahua' => -7,
		'America/Mazatlan' => -7,
		'America/Mexico_City' => -6,
		'America/Monterrey' => -6,
		'Canada/Saskatchewan' => -6,
		'US/Central' => -6,
		'US/Eastern' => -5,
		'US/East-Indiana' => -5,
		'America/Bogota' => -5,
		'America/Lima' => -5,
		'America/Caracas' => -4.5,
		'Canada/Atlantic' => -4,
		'America/La_Paz' => -4,
		'America/Santiago' => -4,
		'Canada/Newfoundland' => -3.5,
		'America/Buenos_Aires' => -3,
		'Greenland' => -3,
		'Atlantic/Stanley' => -2,
		'Atlantic/Azores' => -1,
		'Atlantic/Cape_Verde' => -1,
		'Africa/Casablanca' => 0,
		'Europe/Dublin' => 0,
		'Europe/Lisbon' => 0,
		'Europe/London' => 0,
		'Africa/Monrovia' => 0,
		'Europe/Amsterdam' => 0,
		'Europe/Belgrade' => 1,
		'Europe/Berlin' => 1,
		'Europe/Bratislava' => 1,
		'Europe/Brussels' => 1,
		'Europe/Budapest' => 1,
		'Europe/Copenhagen' => 1,
		'Europe/Ljubljana' => 1,
		'Europe/Madrid' => 1,
		'Europe/Paris' => 1,
		'Europe/Prague' => 1,
		'Europe/Rome' => 1,
		'Europe/Sarajevo' => 1,
		'Europe/Skopje' => 1,
		'Europe/Stockholm' => 1,
		'Europe/Vienna' => 1,
		'Europe/Warsaw' => 1,
		'Europe/Zagreb' => 1,
		'Europe/Athens' => 2,
		'Europe/Bucharest' => 2,
		'Africa/Cairo' => 2,
		'Africa/Harare' => 2,
		'Europe/Helsinki' => 2,
		'Europe/Istanbul' => 2,
		'Asia/Jerusalem' => 2,
		'Europe/Kiev' => 2,
		'Europe/Minsk' => 2,
		'Europe/Riga' => 2,
		'Europe/Sofia' => 2,
		'Europe/Tallinn' => 2,
		'Europe/Vilnius' => 2,
		'Asia/Baghdad' => 3,
		'Asia/Kuwait' => 3,
		'Africa/Nairobi' => 3,
		'Asia/Riyadh' => 3,
		'Asia/Tehran' => 3.5,
		'Europe/Moscow' => 4,
		'Asia/Baku' => 4,
		'Europe/Volgograd' => 4,
		'Asia/Muscat' => 4,
		'Asia/Tbilisi' => 4,
		'Asia/Yerevan' => 4,
		'Asia/Kabul' => 4.5,
		'Asia/Karachi' => 5,
		'Asia/Tashkent' => 5,
		'Asia/Kolkata' => 5.5,
		'Asia/Kathmandu' => 5.75,
		'Asia/Yekaterinburg'=> 6,
		'Asia/Almaty' => 6,
		'Asia/Dhaka' => 6,
		'Asia/Novosibirsk' => 7,
		'Asia/Bangkok' => 7,
		'Asia/Jakarta' => 7,
		'Asia/Krasnoyarsk' => 8,
		'Asia/Chongqing' => 8,
		'Asia/Hong_Kong' => 8,
		'Asia/Kuala_Lumpur' => 8,
		'Australia/Perth' => 8,
		'Asia/Singapore' => 8,
		'Asia/Taipei' => 8,
		'Asia/Ulaanbaatar' => 8,
		'Asia/Urumqi' => 8,
		'Asia/Irkutsk' => 9,
		'Asia/Seoul' => 9,
		'Asia/Tokyo' => 9,
		'Australia/Adelaide'=> 9.5,
		'Australia/Darwin' => 9.5,
		'Asia/Yakutsk' => 10,
		'Australia/Brisbane'=> 10,
		'Australia/Canberra'=> 10,
		'Pacific/Guam' => 10,
		'Australia/Hobart' => 10,
		'Australia/Melbourne' => 10,
		'Pacific/Port_Moresby' => 10,
		'Australia/Sydney' => 10,
		'Asia/Vladivostok' => 11,
		'Asia/Magadan' => 12,
		'Pacific/Auckland' => 12,
		'Pacific/Fiji' => 12
	);


	/**
	 * Get the timezone offset
	 * 
	 * @param string $timezone
	 * @return float
	 * @example parameter "Europe/Zagreb" will return 1
	 * @throws \InvalidArgumentException
	 */
	public static function getOffset($timezone) {
		if (!array_key_exists($timezone, static::$timezones)) {
			throw new \InvalidArgumentException("Invalid timezone passed: {$timezone}");
		}
		return static::$timezoneOffsets[$timezone];
	}


	/**
	 * Get the timezone offset in minutes
	 * 
	 * @param string $timezone
	 * @return float
	 * @throws \InvalidArgumentException
	 */
	public static function getOffsetMinutes($timezone) {
		if (!array_key_exists($timezone, static::$timezones)) {
			throw new \InvalidArgumentException("Invalid timezone passed: {$timezone}");
		}
		return (static::$timezoneOffsets[$timezone] * 60);
	}


	/**
	 * Does given timezone exists or not
	 * 
	 * @param string $timezone
	 * @return bool
	 */
	public static function exists($timezone) {
		return array_key_exists($timezone, static::$timezones);
	}


	/**
	 * Get the date
	 * 
	 * @param string $timezone
	 * @param string $format
	 * @param int $timestamp
	 * @return string
	 */
	public static function date($timezone, $format, $timestamp = null) {
		if ($timestamp === null) {
			$timestamp = time();
		}

		$minutesOffset = static::getOffsetMinutes($timezone);
		$timestamp += $minutesOffset * 60;
		return date($format, $timestamp);
	}


	/**
	 * Get the UTC date from given timezone (this is opposite to date() method in this class)
	 * 
	 * @param string $timezone
	 * @param string $format
	 * @param int $timestamp
	 * @throws Exception
	 * @return string
	 */
	public static function utc($timezone, $format, $timestamp = null) {
		if ($timestamp === null) {
			$timestamp = time();
		}
		
		$minutesOffset = static::getOffsetMinutes($timezone);
		$timestamp -= $minutesOffset * 60;
		return date($format, $timestamp);
	}

}
