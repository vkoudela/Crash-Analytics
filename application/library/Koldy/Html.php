<?php namespace Koldy;

/**
 * This is another utility class that is related to HTML stuff.
 *
 */
class Html {


	/**
	 * Convert (') into (& apos ;)
	 *
	 * @param string $string
	 * @return string
	 */
	public static function apos($string) {
		return str_replace("'", '&apos;', $string);
	}


	/**
	 * Parse quotes and return it with html entities
	 *
	 * @param string $string
	 * @return string
	 * @example " -> & quot ;
	 */
	public static function quotes($string) {
		return str_replace("\"", '&quot;', $string);
	}


	/**
	 * Parse "<" and ">" and return it with html entities
	 *
	 * @param string $string
	 * @return string
	 * @example "<" and ">" -> "&lt;" and "&gt;"
	 */
	public static function tags($string) {
		$string = str_replace('<', '&lt;', $string);
		$string = str_replace('>', '&gt;', $string);
		return $string;
	}


	/**
	 * Truncate the long string properly
	 * 
	 * @param string $string
	 * @param int $length default 80 [optional]
	 * @param string $etc suffix string [optional] default '...'
	 * @param bool $breakWords [optional] default false, true to cut the words in text
	 * @param bool $middle [optional] default false
	 * @return string
	 */
	public static function truncate($string, $length = 80, $etc = '...', $breakWords = false, $middle = false) {
		if ($length == 0) {
			return '';
		}

		if (strlen($string) > $length) {
			$length -= min($length, strlen($etc));

			if (!$breakWords && !$middle) {
				$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length +1));
			}

			if(!$middle) {
				return substr($string, 0, $length) . $etc;
			} else {
				return substr($string, 0, $length /2) . $etc . substr($string, -$length /2);
			}
		} else {
			return $string;
		}
	}


	/**
	 * When having plain text with paragraphs and rows delimited only with new
	 * line and you need to make HTML paragraphs from that omitted with <p>
	 * tag, then use this method.
	 * 
	 * @param string $string text
	 * @example text "Lorem ipsum\n\ndolor sit amet\nperiod." will become "<p>Lorem ipsum</p><p>dolor sit amet<br/>period.</p>"
	 */
	public static function p($string) {
		$string = str_replace("\n\n", '</p><p>', $string);
		$string = str_replace("\n", '<br />', $string);
		return "<p>{$string}</p>";
	}


	/**
	 * This method returns string in "SEO" format good for using in URL's
	 * 
	 * @return string
	 * @example "Your new - title" will become "your-new-title"
	 * @example "Vozač napravio 1500€ štete" will become "vozac-napravio-1500eur-stete"
	 */
	public static function getSeoString($string) {
		if ($string === null) {
			return null;
		}

		$s = strip_tags(trim($string));
		$s = strtolower($s);
		$rpl = array(
			'/(,|;|\!|\?|:|&|\+|\=|-|\'|\/|\*|\t|\n)/' => '-',
			'/(\')/' => '',

			'/≈°/' => 's',
			'/ƒë/' => 'd',
			'/ƒç/' => 'c',
			'/ƒá/' => 'c',
			'/≈æ/' => 'z',
			'/≈†/' => 's',
			'/ƒê/' => 'd',
			'/ƒå/' => 'c',
			'/ƒÜ/' => 'c',
			'/≈Ω/' => 'z',

			'/š/' => 's',
			'/đ/' => 'd',
			'/č/' => 'c',
			'/ć/' => 'c',
			'/ž/' => 'z',
			'/Š/' => 's',
			'/Đ/' => 'd',
			'/Č/' => 'c',
			'/Ć/' => 'c',
			'/Ž/' => 'z',

			'/á/' => 'a',
			'/é/' => 'e',
			'/í/' => 'i',
			'/ó/' => 'o',
			'/ö/' => 'o',
			'/ő/' => 'o',
			'/ú/' => 'u',
			'/ü/' => 'u',
			'/ű/' => 'u',

			'/Á/' => 'A',
			'/É/' => 'E',
			'/Í/' => 'I',
			'/Ó/' => 'O',
			'/Ö/' => 'O',
			'/Ő/' => 'O',
			'/Ú/' => 'U',
			'/Ü/' => 'U',
			'/Ű/' => 'U',

			'/&353;/' => 's',
			'/&273;/' => 'd',
			'/&269;/' => 'c',
			'/&263;/' => 'c',
			'/&382;/' => 'z',
			'/&351;/' => 'S',
			'/&272;/' => 'D',
			'/&268;/' => 'C',
			'/&262;/' => 'C',
			'/&381;/' => 'Z'
		);

		$s = preg_replace(array_keys($rpl), array_values($rpl), $s);

		for ($i = 0; $i<strlen($s); $i++) {
			switch(ord($s[$i])) {
				case 61553:
				case 61656:
				case 61607:
				case 8211:
					$s = str_replace($s[$i], '-', $s);
				break;
			}
		}

		$s = str_replace('\$','-',$s);
		$s = str_replace('%','-',$s);
		$s = str_replace('#','-',$s);
		$s = str_replace('\\','',$s);
		$s = str_replace('¬Æ','-',$s);
		$s = str_replace('‚Äì','-',$s);
		$s = str_replace('^','-',$s);
		$s = str_replace('¬©','-',$s);
		$s = str_replace('√ü','',$s);
		$s = str_replace('(','-',$s);
		$s = str_replace(')','-',$s);
		$s = str_replace('.','-',$s);
		$s = str_replace('[','-',$s);
		$s = str_replace(']','-',$s);
		$s = str_replace('{','-',$s);
		$s = str_replace('}','-',$s);
		$s = str_replace('’','',$s);

		$s = str_replace('€','eur',$s);
		$s = str_replace('$','usd',$s);
		$s = str_replace('£','pound',$s);
		$s = str_replace('¥','yen',$s);

		$s = str_replace(' ','-',$s);
		while (strpos($s, '--') !== false) {
			$s = str_replace('--','-',$s);
		}

		while ($s[0] == '-') {
			$s = substr($s, 1);
		}

		while (substr($s, -1) == '-') {
			$s = substr($s, 0, -1);
		}

		$a = $s;
		$s = '';

		for ($i = 0; $i < strlen($a); $i++) {
			$char = $a[$i];
			$ascii = ord($char);
			if ($char == '-'
				|| ($ascii >= 97 && $ascii <= 122)
				|| ($ascii >= 48 && $ascii <= 57))
			{
				$s .= $char;
			}
		}

		return $s;
	}


	/**
	 * Format given string - equivalent to Ext's String.format()
	 * 
	 * @param string $subject
	 * @param mixed $value1
	 * @param mixed $value2...
	 * @return string
	 * @link http://docs.sencha.com/extjs/4.1.3/#!/api/Ext.String-method-format
	 * @example
	 * 
	 * 		Html::formatString('<div class="{0}">{1}</div>', 'my-class', 'text');
	 * 		will return <div class="my-class">text</div>
	 */
	public static function formatString() {
		$args = func_get_args();
		if (sizeof($args) >= 2) {
			$string = $args[0];
			$argsSize = count($args);
			for ($i = 1; $i < $argsSize; $i++) {
				$num = $i -1;
				$string = str_replace("{{$num}}", $args[$i], $string);
			}
			return $string;
		} else {
			return null;
		}
	}


	/**
	 * Detect URLs in text and replace them with HTML A tag
	 * 
	 * @param string $text
	 * @param string $target optional, default _blank
	 * @return string
	 */
	public static function a($text, $target = null) {
		return preg_replace(
			'@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@',
			"<a href=\"\$1\"" . ($target != null ? " target=\"{$target}\"" : '') . ">$1</a>",
			$text
		);
	}

}
