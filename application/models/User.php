<?php

use Koldy\Db\Model;
use Koldy\Request;

class User extends Model {
	
	/**
	 * Check the user's credentials
	 * @param string $username
	 * @param string $plainPassword
	 * @return boolean
	 */
	public static function auth($username, $plainPassword) {
		/** @var User $user */
		$user = static::fetchOne('username', $username);
		
		if ($user !== false) {
			$password = md5($plainPassword);
			if ($user->pass === $password) {
				return $user;
			}
		}
		
		return false;
	}

	/**
	 * Update login stats
	 */
	public function updateLoginStats() {
		$this->last_login = gmdate('Y-m-d H:i:s');
		$this->last_login_ip = Request::ipWithProxy();
		$this->save();
	}
}