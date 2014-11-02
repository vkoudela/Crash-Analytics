<?php namespace Koldy;

/**
 * The crypting/hashing class. This class provides some common crypt/decrypt
 * features. This class might be changed in the future!
 *
 */
class Crypt {


	const CYPHER = MCRYPT_RIJNDAEL_256;
	const MODE   = MCRYPT_MODE_CBC;


	/**
	 * Check the mcrypt_generic_init return value
	 * 
	 * @param int $code
	 * @throws Exception
	 */
	private static function validateMcryptGenericInit($code) {
		if ($code < 0) {
			switch($code) {
				case -3: throw new Exception('Key length for hashing is incorrect'); break;
				case -4: throw new Exception('Memory allocation problem'); break;
				default: throw new Exception('MCrypt unknown error'); break;
			}
		}
	}


	/**
	 * Encrypt the given string
	 * 
	 * @param string $plaintext
	 * @param string $key
	 * @return string
	 */
	public static function encrypt($plaintext, $key) {
		$td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');

		$maxKeyLength = mcrypt_enc_get_key_size($td);
		if (strlen($key) > $maxKeyLength) {
			throw new Exception('Key length for hashing is incorrect');
		}
		
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		static::validateMcryptGenericInit(@mcrypt_generic_init($td, $key, $iv));
		$crypttext = mcrypt_generic($td, $plaintext);
		mcrypt_generic_deinit($td);
		return base64_encode($iv . $crypttext);
    }


	/**
     * Decrypt the given hashed string. Validate string you get back!
     * 
     * @param string $crypttext
     * @param string $key
     * @return string
     * TODO: Implement integrity check!
     */
	public static function decrypt($crypttext, $key) {
		$crypttext = base64_decode($crypttext);
		$plaintext = '';
		$td        = mcrypt_module_open(self::CYPHER, '', self::MODE, '');

		$maxKeyLength = mcrypt_enc_get_key_size($td);

		if (strlen($key) > $maxKeyLength) {
			throw new Exception('Key length for hashing is incorrect');
		}

		$ivsize    = mcrypt_enc_get_iv_size($td);
		$iv        = substr($crypttext, 0, $ivsize);
		$crypttext = substr($crypttext, $ivsize);

		if ($iv) {
			static::validateMcryptGenericInit(@mcrypt_generic_init($td, $key, $iv));
			$plaintext = mdecrypt_generic($td, $crypttext);
		}

		return trim($plaintext);
	}

}
