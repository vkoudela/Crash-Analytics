<?php namespace Email;

use Koldy\Db\Model;
use Koldy\Mail;
use Koldy\Log;
use Koldy\Request;

class Trigger extends Model {
	
	public static function processRequest(array $params) {
		$data = $params;
		$params = array();
		foreach ($data as $key => $value) {
			$params[strtolower($key)] = $value;
		}
		unset($data);
		
		$where = array();
		$bindings = null;
		
		if (isset($params['package_name']) && $params['package_name'] !== null && trim($params['package_name']) != '') {
			$where[] = '(package IS NULL OR package LIKE :package_name)';
			$bindings['package_name'] = "%{$params['package_name']}%";
		}
		
		if (isset($params['app_version_name']) && $params['app_version_name'] !== null && trim($params['app_version_name']) != '') {
			$where[] = '(package_version IS NULL OR package_version LIKE :package_version)';
			$bindings['package_version'] = "%{$params['app_version_name']}%";
		}
		
		if (isset($params['android_version']) && $params['android_version'] !== null && trim($params['android_version']) != '') {
			$where[] = '(os_version IS NULL OR os_version LIKE :os_version)';
			$bindings['os_version'] = "%{$params['android_version']}%";
		}
		
		if (isset($params['brand']) && $params['brand'] !== null && trim($params['brand']) != '') {
			$where[] = '(brand IS NULL OR brand LIKE :brand)';
			$bindings['brand'] = "%{$params['brand']}%";
		}
		
		if (isset($params['phone_model']) && $params['phone_model'] !== null && trim($params['phone_model']) != '') {
			$where[] = '(model IS NULL OR model LIKE :model)';
			$bindings['model'] = "%{$params['phone_model']}%";
		}
		
		if (isset($params['product']) && $params['product'] !== null && trim($params['product']) != '') {
			$where[] = '(product IS NULL OR product LIKE :product)';
			$bindings['product'] = "%{$params['product']}%";
		}
		
		if (isset($params['country']) && $params['country'] !== null && trim($params['country']) != '') {
			$where[] = '(country IS NULL OR country LIKE :country)';
			$bindings['country'] = "%{$params['country']}%";
		}
		
		if (sizeof($where) == 0) {
			return false;
		}
		
		$where = implode(' AND ', $where);
		
		$query = "
			SELECT
				id,
				name,
				to_emails,
				last_email,
				email_delay_minutes
			FROM
				email_trigger
			WHERE
				{$where}
				AND state = 'waiting'
		";
		
		$records = static::getAdapter()->query($query, $bindings);
		foreach ($records as $r) {
			$send = false;
			
			$now = gmdate('Y-m-d H:i:s');
			if ($r->last_email === null) {
				$send = true;				
			} else {
				$then = new \DateTime($r->last_email);
				$then->modify("+{$r->email_delay_minutes} minute");
				
// 				Log::debug("{$then->format('Y-m-d H:i:s')} < {$now}");
				if ($then->format('Y-m-d H:i:s') < $now) {
					$send = true;
				}
			}
			
			if ($send) {
				static::update(array('state' => 'sending'), $r->id);
				
				$email = Mail::create();
				foreach (explode(',', $r->to_emails) as $address) {
					$email->to($address);
				}
				$email->subject($r->name);
				
				$body = array("DATE AND TIME (GMT): {$now}");
				
				$body[] = "PACKAGE: {$params['package_name']} {$params['app_version_name']}";
				unset($params['package_name'], $params['app_version_name']);
				
				// first append one line data
				foreach ($params as $key => $value) {
					$value = trim($value);
					if ($value != '') {
						if (strpos($value, "\n") === false) {
							$body[] = strtoupper($key) . ": {$value}";
							unset($params[$key]);
						}
					}
				}
				
				if (isset($params['stack_trace'])) {
					$body[] = "STACK TRACE\n" . str_repeat('=', 30) . "\n" . $params['stack_trace'];
					unset($params['stack_trace']);
				}
				
				// append multiple line data
				foreach ($params as $key => $value) {
					$value = trim($value);
					if ($value != '') {
						$body[] = strtoupper($key) . "\n" . str_repeat('=', 30) . "\n" . $value;
						unset($params[$key]);
					}
				}
				
				$email->body(implode("\n\n", $body));
				$email->from('no-reply@' . Request::hostName());
				
				if ($email->send()) {
					Log::notice("Sent e-mail alert '{$r->name}'");
					static::update(array(
						'last_email' => $now,
						'last_update' => $now,
						'state' => 'waiting'
					), $r->id);
				} else {
					Log::error("Can not send e-mail alert '{$r->name}', sender returned false");
					static::update(array(
						'state' => 'waiting'
					), $r->id);
				}
			}
		}
	}
	
}