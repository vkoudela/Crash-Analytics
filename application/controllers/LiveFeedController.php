<?php

use Koldy\View;
use Koldy\Json;
use Koldy\Db\Query;
use BootstrapUI\Input\Select2;
use Koldy\Input;
use Koldy\Validator;
use Koldy\Url;
use Koldy\Db\Expr;
use Koldy\Db;
use Koldy\Log;

class LiveFeedController extends AbstractSessionController {
	
	public function indexAction() {
		Select2::injectDependencies();
		
		$keys = array('package', 'package_version', 'brand', 'phone_model', 'product', 'os_version', 'country');
		$values = array();
		foreach ($keys as $key) {
			$values[$key] = Input::hasGet($key) ? Input::get($key) : null;
		}
		
		return View::create('base')
			->with('title', 'Live Feed')
			->with('page', 'live-feed')
			->with('values', $values);
	}
	
	public function filterAction() {
		// just reroute
		return $this->indexAction();
	}
	
	public function goFilterAjax() {
		$validator = Validator::create(array(
			'package' => 'min:3|max:1024',
			'package_version' => 'min:3|max:255',
			'brand' => 'min:3|max:255',
			'os_version' => 'min:1|max:255',
			'phone_model' => 'min:3|max:255',
			'product' => 'min:3|max:255'
		));
		
		if ($validator->failed()) {
			return BootstrapUI::formResponse()->failedOn($validator);
		}
		
		$data = $validator->getParamsObj();
		$params = array();
		
		if ($data->package !== null) {
			$params['package'] = $data->package;
		}
		
		if ($data->package_version !== null) {
			$params['package_version'] = $data->package_version;
		}
		
		if ($data->brand !== null) {
			$params['brand'] = $data->brand;
		}
		
		if ($data->os_version !== null) {
			$params['os_version'] = $data->os_version;
		}
		
		if ($data->phone_model !== null) {
			$params['phone_model'] = $data->phone_model;
		}
		
		if ($data->product !== null) {
			$params['product'] = $data->product;
		}
		
		// the country is array
		if (Input::hasPost('country')) {
			$params['country'] = implode(',', array_values(Input::post('country')));
		}
		
		if (sizeof($params) == 0) {
			return BootstrapUI::formResponse()->failed('You must define at least one criteria!');
		}
		
		return BootstrapUI::formResponse()->redirect(Url::href('live-feed', 'filter', $params));
	}
	
	public function indexAjax() {
		$where = array(); $bindings = array();
		$params = Input::requireParams('package', 'package_version', 'brand', 'phone_model', 'product', 'os_version', 'country');

		// package
		if ($params->package !== '') {
			$a = explode(',', $params->package);
			$vals = array();
			foreach ($a as $val) {
				$vals[] = "s.package_name = ?";
				$bindings[] = $val;
			}
			$vals = '(' . implode(' OR ', $vals) . ')';
			$where[] = $vals;
		}

		// package_version
		if ($params->package_version !== '') {
			$a = explode(',', $params->package_version);
			$vals = array();
			foreach ($a as $val) {
				$vals[] = "s.app_version_name = ?";
				$bindings[] = $val;
			}
			$vals = '(' . implode(' OR ', $vals) . ')';
			$where[] = $vals;
		}

		// brand
		if ($params->brand !== '') {
			$a = explode(',', $params->brand);
			$vals = array();
			foreach ($a as $val) {
				$vals[] = "s.brand = ?";
				$bindings[] = $val;
			}
			$vals = '(' . implode(' OR ', $vals) . ')';
			$where[] = $vals;
		}

		// phone_model
		if ($params->phone_model !== '') {
			$a = explode(',', $params->phone_model);
			$vals = array();
			foreach ($a as $val) {
				$vals[] = "s.phone_model = ?";
				$bindings[] = $val;
			}
			$vals = '(' . implode(' OR ', $vals) . ')';
			$where[] = $vals;
		}

		// product
		if ($params->product !== '') {
			$a = explode(',', $params->product);
			$vals = array();
			foreach ($a as $val) {
				$vals[] = "s.product = ?";
				$bindings[] = $val;
			}
			$vals = '(' . implode(' OR ', $vals) . ')';
			$where[] = $vals;
		}

		// os_version
		if ($params->os_version !== '') {
			$a = explode(',', $params->os_version);
			$vals = array();
			foreach ($a as $val) {
				$vals[] = "s.android_version = ?";
				$bindings[] = $val;
			}
			$vals = '(' . implode(' OR ', $vals) . ')';
			$where[] = $vals;
		}

		// country
		if ($params->country !== '') {
			$a = explode(',', $params->country);
			$vals = array();
			foreach ($a as $val) {
				$vals[] = "s.country = ?";
				$bindings[] = $val;
			}
			$vals = '(' . implode(' OR ', $vals) . ')';
			$where[] = $vals;
		}

		
		if (sizeof($where) == 0) {
			$where = '1';
		} else {
			$where = implode(' AND ', $where);
		}
		
		$query = "
			SELECT
				s.id,
				s.created_at,
				s.package_name,
				s.app_version_name as package_version,
				s.brand,
				s.phone_model,
				s.product,
				s.android_version,
				s.country,
				s.stack_trace
			FROM
				crash_submit s
			WHERE
				{$where}
			ORDER BY s.created_at DESC
			LIMIT 0, 50
		";
		
		$records = Db::getAdapter()->query($query, $bindings);
		$data = array();
		foreach ($records as $r) {
			if ($r->stack_trace !== null) {
				$lines = explode("\n", $r->stack_trace);
				$stackTraceSummary = array();
				$more = 0;
				foreach ($lines as $line) {
					$ord = ord(substr($line, 0, 1));
					if (($ord >= 65 && $ord <= 90) || ($ord >= 97 && $ord <= 122)) {
						if (sizeof($stackTraceSummary) < 2) {
							$stackTraceSummary[] = trim($line);
						} else {
							$more++;
						}
					}
				}
				
				if ($more > 0) {
					$stackTraceSummary[] = "... and {$more} more line(s)";
				}
				$stackTraceSummary = implode("\n", $stackTraceSummary);
			} else {
				$stackTraceSummary = null;
			}
			
			$data[] = array(
				'id' => (int) $r->id,
				'created_at' => Misc::userDate('H:i:s', $r->created_at),
				'package' => "{$r->package_name} {$r->package_version}",
				'device' => "{$r->brand}<br/>{$r->phone_model}",
				'product' => ($r->phone_model != $r->product) ? $r->product : null,
				'os' => "Android {$r->android_version}",
				'country' => $r->country,
				'stack_trace' => $stackTraceSummary
			);
		}
		
		return Json::create(array(
			'success' => true,
			'time' => Misc::userDate('Y-m-d H:i:s'),
			'data' => $data
		));
	}
	
	public function getStackTraceAjax() {
		$params = Input::requireParams('id');
		$id = (int) $params->id;
		
		if ($id <= 0) {
			throw new Exception('Invalid ID');
		}
		
		$submit = Crash\Submit::fetchOne('id', $id);
		if ($submit === false) {
			throw new Exception('Can not find stack trace');
		}
		
		return Json::create(array(
			'success' => true,
			'stack_trace' => $submit->stack_trace
		));
	}
}