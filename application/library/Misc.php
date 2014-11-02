<?php

use Koldy\Session;
use Koldy\Timezone;

class Misc {

	/**
	 * Get time in user's timezone from UTC
	 * @param string $format
	 * @param string|int $timestamp
	 * @return string
	 */
	public static function userDate($format, $timestamp = null) {
		if ($timestamp === null) {
			$timestamp = time();
		} else {
			if (!is_numeric($timestamp) && !is_object($timestamp)) {
				$timestamp = strtotime($timestamp);
			} else if ($timestamp instanceof DateTime) {
				$timestamp = $timestamp->getTimestamp();
			}
		}

		$user = Session::get('user');
		return Timezone::date($user['timezone'], $format, $timestamp);
	}

	/**
	 * Get UTC time from user's timezone
	 * @param string $format
	 * @param string|int $timestamp
	 * @return string
	 */
	public static function utcFromUser($format, $timestamp = null) {
		if ($timestamp === null) {
			$timestamp = time();
		} else {
			if (!is_numeric($timestamp) && !is_object($timestamp)) {
				$timestamp = strtotime($timestamp);
			} else if ($timestamp instanceof DateTime) {
				$timestamp = $timestamp->getTimestamp();
			}
		}

		$user = Session::get('user');
		return Timezone::utc($user['timezone'], $format, $timestamp);
	}

	/**
	 * Get the timezone offset of current user in minutes
	 * @return number
	 */
	public static function getUserTimezoneOffsetInMinutes() {
		$user = Session::get('user');
		return Timezone::$timezoneOffsets[$user['timezone']] * 60;
	}
}
