<?php

use Koldy\View;
use Koldy\Url;
use Koldy\Input;
use Koldy\Validator;
use Koldy\Application;
use Koldy\Db\ResultSet;
use Koldy\Log;
use Koldy\Timezone;
use Koldy\Session;
use Koldy\Db\Select;

class ReportsController extends AbstractSessionController {
	
	public function indexAction() {
		$panel = Bootstrap::panel('Search for crash reports')->color('blue');
		
		$form = Bootstrap::form(Url::href('reports', 'search'))
			->horizontal()
			->method('get')
			->add(BootstrapUI::select2('package_id', 'package', Package::getSelectOptions(true)))
			->add(BootstrapUI::select2('brand_id', 'brand', Brand::getSelectOptions(true)))
			->add(BootstrapUI::select2('os_version_id', 'OS and version', Version::getSelectOptions(true)))
			->add(BootstrapUI::select2('country_id', 'country', Country::getSelectOptions(true)))
// 			->add(BootstrapUI::select2('provider_id', 'provider', Provider::getSelectOptions(true)))
			->add(Bootstrap::textfield('date_from', null, 'date and time from')->type('datetime-local'))
			->add(Bootstrap::textfield('date_to', null, 'date and time to')->type('datetime-local'))
			->addSubmit('Search');
		$panel->content($form);
		
		return View::create('base')
			->with('title', 'Search for reports')
			->with('content', Bootstrap::row()->add(12, $panel));
	}
	
	public function searchAction() {
		$title = 'Search results';
		
		if (sizeof(Input::get()) == 1) {
			if (Input::hasGet('provider_id')) {
				$e = Provider::fetchOne(Input::get('provider_id'));
				$title = "Search results for internet provider <strong>{$e->name}</strong>";

			} else if (Input::hasGet('brand_id')) {
				$e = Brand::fetchOne(Input::get('brand_id'));
				$title = "Search results for brand <strong>{$e->name}</strong>";

			} else if (Input::hasGet('package_id')) {
				$e = Package::fetchOne(Input::get('package_id'));
				$title = "Search results for package <strong>{$e->name}</strong>";

			} else if (Input::hasGet('package_version_id')) {
				$e = Package\Version::fetchOne(Input::get('package_version_id'));
				$p = Package::fetchOne($e->package_id);
				$title = "Search results for <strong>{$p->name}</strong> version {$e->value}";
			}
		}
		
		$packageId = (int) Input::get('package_id');
		$packageVersionId = (int) Input::get('package_version_id');
		$brandId = (int) Input::get('brand_id');
		$osVersionId = (int) Input::get('os_version_id');
		$countryId = (int) Input::get('country_id');
		$providerId = (int) Input::get('provider_id');
		$productId = (int) Input::get('product_id');
		$modelId = (int) Input::get('model_id');
		$dateFrom = Input::get('date_from');
		$dateTo = Input::get('date_to');
		$stackTraceId = (int) Input::get('stack_trace_id');
		
		$table = BootstrapUI::tableRemote()
			->title($title)
			->extraParams(array(
				'package_id' => $packageId,
				'package_version_id' => $packageVersionId,
				'brand_id' => $brandId,
				'os_version_id' => $osVersionId,
				'country_id' => $countryId,
				'provider_id' => $providerId,
				'product_id' => $productId,
				'model_id' => $modelId,
				'date_from' => $dateFrom,
				'date_to' => $dateTo,
				'stack_trace_id' => $stackTraceId
			))
			->column('country_name', '', 20)
			->column('created_at', 'time', 85)
			->column('package_name', 'package')
			->column('package_version', 'version')
			->column('brand_name', 'brand')
			->column('os_version_name', 'OS version', 105)
			->column('country', '')
			->column('action', '', 30)
			
			->sortableColumns(array('created_at', 'package_name', 'package_version', 'brand_name', 'os_version_name', 'cnt'))
			->sortField('created_at', 'desc');
		
		$table->panel()->addHeaderElement(Bootstrap::button('Back')->color('red')->size('xs')->setAttribute('onclick', 'window.history.back()'));
		
		return View::create('base')
			->with('title', $title)
			->with('content', Bootstrap::row()->add(12, $table));
	}
	
	public function searchAjax() {
		$validator = Validator::create(array(
			'package_id' => 'required|integer',
			'package_version_id' => 'required|integer',
			'brand_id' => 'required|integer',
			'os_version_id' => 'required|integer',
			'product_id' => 'required|integer',
			'model_id' => 'required|integer',
			'country_id' => 'required|integer',
			'provider_id' => 'required|integer',
			'date_from' => null,
			'date_to' => null,
			'stack_trace_id' => 'required|integer'
		));
		
		if ($validator->failed()) {
			Application::throwError(400, 'Bad request');
		} else {
			$params = $validator->getParamsObj();
			$prms = array();
			
			$query = new ResultSet();
			$count = new Select();
			$count->from('crash_archive', 'a')->field('COUNT(*)', 'total');
			
			$query->from('crash_archive', 'a')
				->field('a.id')
				->field('a.created_at')
				
				->leftJoin('package p', 'p.id', '=', 'a.package_id')
				->field('p.name', 'package_name')
				
				->leftJoin('package_version pv', 'pv.id', '=', 'a.package_version_id')
				->field('pv.value', 'package_version')
				
				->leftJoin('brand b', 'b.id', '=', 'a.brand_id')
				->field('b.name', 'brand_name')
				
				->leftJoin('stack_trace st', 'st.id', '=', 'a.stack_trace_id')
				->field('st.summary', 'stack_trace')
				
				->leftJoin('version v', 'v.id', '=', 'a.os_version_id')
				->field('v.os', 'os_name')
				->field('v.name', 'os_version_name')
				
				->leftJoin('country c', 'c.id', '=', 'a.country_id')
				->field('c.country', 'country_name')
				->field('c.tld', 'tld');
			
			if ($params->date_from !== null) {
				$dateFrom = new DateTime($params->date_from);
				$query->where('a.created_at', '>=', Misc::utcFromUser('Y-m-d H:i:s', $dateFrom));
				$count->where('a.created_at', '>=', Misc::utcFromUser('Y-m-d H:i:s', $dateFrom));
				$prms['date_from'] = $params->date_from;
			}
			
			if ($params->date_to !== null) {
				$dateTo = new DateTime($params->date_to);
				$query->where('a.created_at', '<', Misc::utcFromUser('Y-m-d H:i:s', $dateTo));
				$count->where('a.created_at', '<', Misc::utcFromUser('Y-m-d H:i:s', $dateTo));
				$prms['date_to'] = $params->date_to;
			}
			
			if ($params->package_id > 0) {
				$query->where('a.package_id', $params->package_id);
				$count->where('a.package_id', $params->package_id);
				$prms['package_id'] = $params->package_id;
			}
			
			if ($params->package_version_id > 0) {
				$query->where('a.package_version_id', $params->package_version_id);
				$count->where('a.package_version_id', $params->package_version_id);
				$prms['package_version_id'] = $params->package_version_id;
			}
			
			if ($params->brand_id > 0) {
				$query->where('a.brand_id', $params->brand_id);
				$count->where('a.brand_id', $params->brand_id);
				$prms['brand_id'] = $params->brand_id;
			}
			
			if ($params->os_version_id > 0) {
				$query->where('a.os_version_id', $params->os_version_id);
				$count->where('a.os_version_id', $params->os_version_id);
				$prms['os_version_id'] = $params->os_version_id;
			}
			
			if ($params->product_id > 0) {
				$query->where('a.product_id', $params->product_id);
				$count->where('a.product_id', $params->product_id);
				$prms['product_id'] = $params->product_id;
			}
			
			if ($params->model_id > 0) {
				$query->where('a.model_id', $params->model_id);
				$count->where('a.model_id', $params->model_id);
				$prms['model_id'] = $params->model_id;
			}
			
			if ($params->country_id > 0) {
				$query->where('a.country_id', $params->country_id);
				$count->where('a.country_id', $params->country_id);
				$prms['country_id'] = $params->country_id;
			}
			
			if ($params->provider_id > 0) {
				$query->where('a.provider_id', $params->provider_id);
				$count->where('a.provider_id', $params->provider_id);
				$prms['provider_id'] = $params->provider_id;
			}
			
			if ($params->stack_trace_id > 0) {
				$query->where('a.stack_trace_id', $params->stack_trace_id);
				$count->where('a.stack_trace_id', $params->stack_trace_id);
				$prms['stack_trace_id'] = $params->stack_trace_id;
			}
			
			if (sizeof($prms) == 1) {
				// speed up count(*) ... because we have that precalculated
				if (isset($prms['stack_trace_id'])) {
					$count = new Select();
					$count->from('stack_trace')
						->field('total')
						->where('id', $prms['stack_trace_id']);

				} else if (isset($prms['brand_id'])) {
					$count = new Select();
					$count->from('brand')
						->field('total')
						->where('id', $prms['brand_id']);

				} else if (isset($prms['package_id'])) {
					$count = new Select();
					$count->from('package')
						->field('total')
						->where('id', $prms['package_id']);

				} else if (isset($prms['package_version_id'])) {
					$count = new Select();
					$count->from('package_version')
						->field('total')
						->where('id', $prms['package_version_id']);

				} else if (isset($prms['os_version_id'])) {
					$count = new Select();
					$count->from('version')
						->field('total')
						->where('id', $prms['os_version_id']);

				} else if (isset($prms['country_id'])) {
					$count = new Select();
					$count->from('country')
						->field('total')
						->where('id', $prms['country_id']);

				} else if (isset($prms['provider_id'])) {
					$count = new Select();
					$count->from('provider')
						->field('total')
						->where('id', $prms['provider_id']);

				} else if (isset($prms['model_id'])) {
					$count = new Select();
					$count->from('phone_model')
						->field('total')
						->where('id', $prms['model_id']);
				}
			}

			$query->setCountQuery($count);

			return BootstrapUI::tableRemoteResponse()
				->column('country_name', function($value, $row) {
					if ($row['tld'] !== null) {
						$country = \Koldy\Html::quotes($row['country_name']);
						return '<img src="' . \Koldy\Url::link("img/flag/{$row['tld']}.png") . '" title="' . $country . '" />';
					} else {
						return '';
					}
				})
				->column('created_at', function($value, $row) {
					$user = \Koldy\Session::get('user');
					
					return \Koldy\Timezone::date($user['timezone'], 'd.m.Y', strtotime($value))
						. '<br/>'
						. \Koldy\Timezone::date($user['timezone'], 'H:i:s', strtotime($value));
				})
				->column('package_name', function($value, $row) {
					$html = $value;
					
					$html .= "<details><summary>View stack trace summary</summary><pre class=\"text-danger\">{$row['stack_trace']}</pre></details>";
					
					return $html;
				})
				->column('package_version')
				->column('brand_name')
				->column('os_version_name', function($value, $row) {
					return "{$row['os_name']} {$row['os_version_name']}";
				})
				->column('country')
				->column('action', function($value, $row) {
					return \Bootstrap::anchor(\Bootstrap::icon('eye-open'), \Koldy\Url::href('report', $row['id']))->title('View report')->size('xs');
				})
				
				->resultSet($query)
				->handle();
		}
	}
	
	public function packageAction() {
		$content = array();
		
		$panel = Bootstrap::panel('Filter')->collapsible(true);
		$form = BootstrapUI::form()
			->horizontal()
			->add(Bootstrap::textfield('name', null, 'name'))
			->addSubmit('Filter');
		
		$panel->content($form);
		$content[] = Bootstrap::row()->add(12, $panel);
		
		return View::create('base')
			->with('title', 'Package reports')
			->with('content', $content);
	}
	
}