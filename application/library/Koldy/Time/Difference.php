<?php namespace Koldy\Time;

/**
 * Get the relative time when comparing two dates. The class can accept different
 * formats according to localization and translation. Just make sure that you
 * have configured it right.
 * 
 * @TODO završiti ovo
 * @example 1 day ago, 45 minutes ago, now
 */
class Difference {
	
	/**
	 * the translation array
	 * @var array
	 */
	protected static $translation = array(
		"ago" => "ago",
		"before" => "before",
		"just-now" => "just now",
		"seconds" => "seconds",
		"1minute" => "minute",
		"minutes" => "minutes",
		"1hour" => "hour",
		"hours" => "hours",
		"1day" => "day",
		"days" => "days",
		"1month" => "month",
		"months" => "months"
	);
	
	/**
	 * the date object that everything is compared to
	 * @var Xcms_Date
	 */
	private $referenceDate = null;
	
	/**
	 * object that will be used if there is no object passed to methods
	 * @var Xcms_Date
	 */
	private $compareTo = null;
	
	/**
	 * the format to display the time difference
	 * @var string
	 */
	protected static $format = "{difference} {measure} {ago}";
	
	/**
	 * construct the object
	 * @param int $referenceDate
	 * @param int $compareTo timestamp in most cases, this will be current time
	 */
	public function __construct($referenceDate, $compareTo = null) {
		$this->referenceDate = is_string($referenceDate) ? strtotime($referenceDate) : $referenceDate;
		$this->compareTo = is_string($compareTo) ? strtotime($compareTo) : $compareTo;
	}
	
	/**
	 * set the display format
	 * @param string $format
	 * @example {before} - text 'before', {difference} - number difference, {measure} - e.g. 'minutes', {ago} - text 'ago'
	 */
	public static function setFormat($format) {
		static::$format = $format;
	}
	
	/**
	 * get the time difference text
	 * @param int $compareTo OPTIONAL
	 * @return string
	 */
	public function get($compareTo = null) {
		if ($compareTo === null) {
			if ($this->compareTo === null) {
				\Koldy\Application::error(500, 'Can not calculate time difference with non-existing time');
			} else {
				$compareTo = $this->compareTo;
			}
		}
		
		$difference = ($compareTo - $this->referenceDate);
		
		if ($difference < 10) {
			return static::$translation['just-now'];
		}
		
		$measure = null;
		
		if ($difference < 60) {
			$diff = $difference;
			$measure = "seconds";
		}
		
		if ($measure === null && $difference < 150) { // 
			$diff = 1;
			$measure = "1minute";
		}
		
		if ($measure === null && $difference < 3600) { // 60*60
			$diff = floor($difference / 60);
			$measure = "minutes";
		}
		
		if ($measure === null && $difference < 7200) { // 60*60 + 3600
			$diff = 1;
			$measure = "1hour";
		}
		
		if ($measure === null && $difference < 86400) { // 60*60*24
			$diff = floor($difference / 3600); // 60*60
			$measure = "hours";
		}
		
		if ($measure === null && $difference < 172800) { // 60*60*24 + 60*60*24
			$diff = 1;
			$measure = "1day";
		}
		
		if ($measure === null && $difference < 2592000) { // 60*60*24*30
			$diff = floor($difference / 86400); // 3600 * 24
			$measure = "days";
		}
		
		if ($measure === null && $difference < 5184000) { // 60*60*24*30 + 3600*24*30
			$diff = 1;
			$measure = "1month";
		}
		
		if ($measure === null && $difference < 10368000) { // 60*60*24*30*4
			$diff = floor($difference / 2592000); // 60*60*24*30
			$measure = "months";
		}
		
		if ($measure === null) {
			// TODO: Implement date translation/localization here
			return date("Y-m-d H:i:s", strtotime($compareTo));
		}
		
		$text = static::$format;
		$text = str_replace("{before}", static::$translation['before'], $text);
		$text = str_replace("{difference}", $diff, $text);
		$text = str_replace("{measure}", static::$translation[$measure], $text);
		$text = str_replace("{ago}", static::$translation['ago'], $text);
		return $text;
	}
	
	public function __toString() {
		if ($this->referenceDate !== null && $this->compareTo !== null) {
			return $this->get();
		} else {
			return '[Time Difference Error]';
		}
	}
}