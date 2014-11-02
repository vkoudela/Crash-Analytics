<?php namespace Koldy;

/**
 * This is utility class that has some common converting methods.
 * Just take a look at the methods and their PHPdoc
 *
 */
class Convert {


	/**
	 * Measures for bytes
	 * 
	 * @var array
	 */
	private static $measure = array('B', 'KB', 'MB', 'GB', 'TB', 'PT');


	/**
	 * Get file's measure
	 * 
	 * @param double $size
	 * @param int $count
	 * @param int $round
	 * @return string
	 */
	private static function getMeasure($size, $count = 0, $round = null) {
		if ($size >= 1024) {
			return self::getMeasure($size / 1024, ++$count);
		} else {
			return round($size, $round) . ' ' . self::$measure[$count];
		}
	}


	/**
	 * Get bytes size as string
	 * 
	 * @param int $bytes
	 * @param int $round round to how many decimals
	 * @return string
	 * @example 2048 will return 2 KB
	 */
	public static function bytesToString($bytes, $round = null) {
		return self::getMeasure($bytes, 0, $round);
	}


	/**
	 * Get the number of bytes from string
	 * 
	 * @param string $string
	 * @return number
	 * @example 1M will return 1048576 
	 */
	public static function stringToBytes($string) {
		$original = trim($string);
		$number = (int) $original;

		if ($number === $original) {
			return $number;
		} else {
			$char = strtoupper(substr($original, -1, 1));
			switch($char) {
				case 'K': // KILO
					return $number * 1024;
					break;

				case 'M': // MEGA
					return $number * 1024 * 1024;
					break;

				case 'G': // GIGA
					return $number * 1024 * 1024 * 1024;
					break;

				case 'T': // TERA
					return $number * 1024 * 1024 * 1024 * 1024;
					break;

				case 'P': // PETA
					return $number * 1024 * 1024 * 1024 * 1024 * 1024;
					break;

				// TODO: Implement next with mbstrings
			}
		}
	}


	/**
	 * Convert kilogram (kg) to pounds (lb)
	 * 
	 * @param float $kilograms
	 * @return float
	 */
	public static function kilogramToPounds($kilograms) {
		return $kilograms * 2.20462262;
	}


	/**
	 * Convert pounds (lb) to kilograms (kg)
	 * 
	 * @param float $pounds
	 * @return float
	 */
	public static function poundToKilograms($pounds) {
		return $pounds / 2.20462262;
	}


	/**
	 * Convert meter (m) to feets (ft)
	 * 
	 * @param float $meters
	 * @return float
	 */
	public static function meterToFeet($meters) {
		return $meters * 3.2808399;
	}


	/**
	 * Convert foot (ft) to meters (m)
	 * 
	 * @param float $feets
	 * @return float
	 */
	public static function footToMeters($feets) {
		return $feets / 3.2808399;
	}


	/**
	 * Convert centimeters (cm) to inchs (in)
	 * 
	 * @param float $centimeters
	 * @return float
	 */
	public static function centimeterToInchs($centimeters) {
		return $centimeters * 0.393700787;
	}


	/**
	 * Convert inchs (in) to centimeters (cm)
	 * 
	 * @param float $inchs
	 * @return float
	 */
	public static function inchToCentimeters($inchs) {
		return $inchs / 0.393700787;
	}


	/**
	 * Convert given string into proper UTF-8 string
	 *
	 * @param string $string
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 * @throws \Koldy\Exception
	 *
	 * @author Simon Brüchner (@powtac)
	 * @link http://php.net/manual/en/function.utf8-encode.php#102382
	 */
	public static function stringToUtf8($string) {
		if (!is_string($string)) {
			throw new \InvalidArgumentException('Invalid argument; expected string, got: ' . gettype($string));
		}

		if(!mb_check_encoding($string, 'UTF-8') || !($string === mb_convert_encoding(mb_convert_encoding($string, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32'))) {

			$string = mb_convert_encoding($string, 'UTF-8');

			if (!mb_check_encoding($string, 'UTF-8')) {
				throw new Exception('Can not convert given string to UTF-8');
			}
		}

		return $string;
	}

}
