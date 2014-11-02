<?php namespace Koldy\Convert;

/**
 * Someday, you'll encounter the situtation when PHP can't handle really big numbers. If you search the internet, you'll
 * find out that you should be using PHP's BC Math lib that treats big numbers as string, which is fine. But, after a
 * while, you'll probably need to write those big numbers in shorter form. This class does exactly that. By default,
 * we're using all numbers, lower and uppercase ASCII latters for conversions which gives us numeric system per
 * base 62. If you want to use the number/letter combination you want, then please extend this class and override
 * getAvailableNumbers() method.
 *
 * This class requires BC Math which is available since PHP 4.0.4
 * @link http://php.net/manual/en/book.bc.php
 */
class NumericNotation {


	/**
	 * Get the numbers/alphabet for number<->string conversions
	 *
	 * @return array
	 */
	protected static function getAvailableNumbers() {
		return array(
			0 => '0', 10 => 'a', 20 => 'k', 30 => 'u', 40 => 'E', 50 => 'O', 60 => 'Y',
			1 => '1', 11 => 'b', 21 => 'l', 31 => 'v', 41 => 'F', 51 => 'P', 61 => 'Z',
			2 => '2', 12 => 'c', 22 => 'm', 32 => 'w', 42 => 'G', 52 => 'Q',
			3 => '3', 13 => 'd', 23 => 'n', 33 => 'x', 43 => 'H', 53 => 'R',
			4 => '4', 14 => 'e', 24 => 'o', 34 => 'y', 44 => 'I', 54 => 'S',
			5 => '5', 15 => 'f', 25 => 'p', 35 => 'z', 45 => 'J', 55 => 'T',
			6 => '6', 16 => 'g', 26 => 'q', 36 => 'A', 46 => 'K', 56 => 'U',
			7 => '7', 17 => 'h', 27 => 'r', 37 => 'B', 47 => 'L', 57 => 'V',
			8 => '8', 18 => 'i', 28 => 's', 38 => 'C', 48 => 'M', 58 => 'W',
			9 => '9', 19 => 'j', 29 => 't', 39 => 'D', 49 => 'N', 59 => 'X'
		);
	}


	/**
	 * Convert decimal number into your numeric system
	 *
	 * @param string $number
	 * @return string
	 */
	public static function dec2big($number) {
		$alphabet = static::getAvailableNumbers();

		$number = trim((string) $number);
		$decimals = strlen($number);

		if (strlen($number) == 0) {
			return 0;
		}

		$mod = count($alphabet);
		$s = '';

		do {
			$x = bcdiv($number, $mod, 0);
			$left = bcmod($number, $mod);
			$char = $alphabet[$left];
			$s = $char . $s;

			$number = $x;
		} while ($x != '0');

		return $s;
	}


	/**
	 * The reverse procedure, convert number from your numeric system into decimal number
	 *
	 * @param string $alpha
	 * @return string because real number can reach the MAX_INT
	 */
	public static function big2dec($alpha) {
		if (strlen($alpha) <= 0) {
			return 0;
		}

		$alphabet = array_flip(static::getAvailableNumbers());
		$mod = count($alphabet);

		$x = 0;
		for ($i = 0, $j = strlen($alpha) -1; $i < strlen($alpha); $i++, $j--) {
			$char = substr($alpha, $j, 1);
			$val = $alphabet[$char];
			$x = bcadd($x, bcmul($val, bcpow($mod, $i)));
		}

		return $x;
	}

}
