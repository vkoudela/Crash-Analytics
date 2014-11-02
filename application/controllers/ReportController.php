<?php

use Koldy\View;
use Koldy\Url;
use Crash\Archive as CrashArchive;
use Koldy\Application;
use Koldy\Convert;
use Koldy\Timezone;
use Koldy\Cache;
use Koldy\Redirect;

class ReportController extends AbstractSessionController {
	
	/**
	 * Get the search URL
	 * @param string $what
	 * @param int $id
	 * @return string
	 */
	private function getSearchUrl($what, $id) {
		return Url::href('reports', 'search', array($what => $id));
	}
	
	/**
	 * Get search link
	 * @param string $variable
	 * @param int $id
	 * @param string $text
	 * @return string
	 */
	private function getSearchLink($variable, $id, $text) {
		return "<a href=\"{$this->getSearchUrl($variable, $id)}\" title=\"Search this\">{$text}</a> ";
	}
	
	/**
	 * Get red label with number
	 * @param int $count
	 * @return \Bootstrap\Label
	 */
	private function getLabel($count) {
		return Bootstrap::label($count)->color('red');
	}
	
	public function viewAction() {
		$id = Url::getVar(1);
		// todo: ubaciti ovo u cache i odatle cupat van
		$cacheKey = "report-{$id}";
		
		$crash = CrashArchive::fetchOne($id);
		if ($crash === false) {
			Application::throwError(404, 'Can not find crash report ' . $id);
		}
		
		$title = "Crash Report #{$id}";
		$content = array();
		
		$panel = Bootstrap::panel($title)->color('blue')->addHeaderElement(Bootstrap::button('Back')->setAttribute('onclick', 'window.history.back()')->color('red')->size('xs'));
		$table = Bootstrap::table();
		
		$table->column('id', '')->column('value', '');
		
		// time
		$table->row(array(
			'id' => 'time',
			'value' => Misc::userDate('Y-m-d H:i:s', $crash->created_at) . ' (' . $this->user['timezone'] . ')'
		));
		
		// package
		$e = $crash->getPackage();
		$v = $crash->getPackageVersion();
		$table->row(array(
			'id' => 'package and version',
			'value' => implode(' ', array(
				(($e !== null) ? "{$this->getSearchLink('package_id', $e->id, $e->name)} {$this->getLabel($e->total)}" : 'unknown'),
				(($v !== null) ? "{$this->getSearchLink('package_version_id', $v->id, $v->value)} {$this->getLabel($v->total)}" : 'unknown'),
			))
		));
		
		// device
		$value = '';
		$e = $crash->getBrand();
		if ($e === null) {
			$value .= 'unknown brand<br/>';
		} else {
			$value .= "{$this->getSearchLink('brand_id', $e->id, $e->name)} {$this->getLabel($e->total)}<br/>";
		}
		
		$e = $crash->getPhoneModel();
		if ($e === null) {
			$value .= 'unknown phone model<br/>';
		} else {
			$value .= "{$this->getSearchLink('model_id', $e->id, $e->name)} {$this->getLabel($e->total)}<br/>";
		}
		
		$table->row(array(
			'id' => 'device',
			'value' => substr($value, 0, -5)
		));
		
		// product
		$e = $crash->getProduct();
		if ($e !== null) {
			$table->row(array(
				'id' => 'product name',
				'value' => "{$this->getSearchLink('product_id', $e->id, $e->name)} {$this->getLabel($e->total)}"
			));
		}
		
		// os
		$e = $crash->getOsVersion();
		$table->row(array(
			'id' => 'OS',
			'value' => ($e === null) ? 'unknown' : "{$this->getSearchLink('os_version_id', $e->id, "{$e->os} {$e->name}")} {$this->getLabel($e->total)}"
		));
		
		// user comment
		if ($crash->user_comment !== null && trim($crash->user_comment) != '') {
			$table->row(array(
				'id' => 'user comment',
				'value' => $crash->user_comment
			));
		}
		
		// user email
		if ($crash->user_email !== null && trim($crash->user_email) != '') {
			$table->row(array(
				'id' => 'user email',
				'value' => $crash->user_email
			));
		}
		
		// app lifetime
		if ($crash->user_app_start_date !== null && $crash->user_crash_date !== null) {
			$table->row(array(
				'id' => 'app lifetime',
				'value' => "{$crash->user_app_start_date}<br/>{$crash->user_crash_date} (duration: {$this->duration($crash->user_app_start_date, $crash->user_crash_date)})"
			));
		}
		
		// memory usage
		$table->row(array(
			'id' => 'available / total memory size',
			'value' => Convert::bytesToString($crash->available_mem_size) . ' / ' . Convert::bytesToString($crash->total_mem_size)
		));
		
		// country
		if ($crash->country_id !== null) {
			$country = Country::fetchOne($crash->country_id);
			$table->row(array(
				'id' => 'country',
				'value' => "<img src=\"" . Url::link("img/flag/{$country->tld}.png") . "\" /> <a href=\"" . Url::href('reports', 'search', array('country_id' => $crash->country_id)) . "\">{$country->country} (" . strtoupper($country->tld) . ")</a> " . Bootstrap::label($country->total)->color('red')
			));
		}
		
		// provider
		if ($crash->provider_id !== null) {
			$e = Provider::fetchOne($crash->provider_id);
			$table->row(array(
				'id' => 'internet provider',
				'value' => "{$this->getSearchLink('provider_id', $e->id, $e->name)} {$this->getLabel($e->total)}"
			));
		}
		
		$metas = $crash->getMetas();
		
		$toTabs = array();
		foreach ($metas as $key => $value) {
			if ($key != 'stack_trace') {
				if (strpos(trim($value), "\n") === false) {
					$table->row(array(
						'id' => str_replace('_', ' ', $key),
						'value' => (trim($value) == '') ? '<em>empty</em>' : $value
					));
				} else {
					$toTabs[] = $key;
				}
			}
		}
		
		$toTabsUnknown = array();
		$unknownMetas = $crash->getUnknownMetas();
		foreach ($unknownMetas as $key => $value) {
			if (strpos(trim($value), "\n") === false) {
				$table->row(array(
					'id' => str_replace('_', ' ', $key),
					'value' => ((trim($value) == '') ? '<em>empty</em>' : $value) . ' ' . Bootstrap::label('unknown meta')->color('lightblue')
				));
			} else {
				$toTabsUnknown[] = $key;
			}
		}
		
		if ($crash->stack_trace_id !== null) {
			$table->row(array(
				'id' => 'find reports with this stack trace',
				'value' => Bootstrap::anchor(\Bootstrap::icon('search'), Url::href('reports', 'search', array('stack_trace_id' => $crash->stack_trace_id)))->asButton()->color('red')->size('xs')
			));
		}
		
		$panel->content($table);
		$content[] = Bootstrap::row()->add(12, $panel);
		
		$tabs = Bootstrap::nav();
		if (isset($metas['stack_trace'])) {
			$tabs->addLink('stack trace', "<pre class=\"text-danger\">{$metas['stack_trace']}</pre>");
		} else if ($crash->stack_trace_id !== null) {
			$stackTrace = Stack\Trace::fetchOne($crash->stack_trace_id);
			$tabs->addLink('stack trace summary', "<pre class=\"text-danger\">{$stackTrace->summary}</pre>");
		}
		
		if (sizeof($toTabs) > 0) {
			foreach ($toTabs as $key) {
				$tabs->addLink(str_replace('_', ' ', $key), "<pre>{$metas[$key]}</pre>");
			}
		}
		
		if (sizeof($toTabsUnknown) > 0) {
			foreach ($toTabsUnknown as $key) {
				$tabs->addLink(str_replace('_', ' ', $key) . ' ' . Bootstrap::label('?')->color('lightblue'), "<pre>{$unknownMetas[$key]}</pre>");
			}
		}
		
		if ($tabs->count() > 0) {
			$content[] = Bootstrap::row()->add(12, $tabs);
		}
		
		if ($crash->stack_trace_id !== null) {
			$content[] = Bootstrap::row()->add(12, Bootstrap::panel('This error count per day', new Chart\StackTracesPerDay($crash->stack_trace_id, 30))->color('red'));
		}
		
		return View::create('base')
			->with('title', $title)
			->with('content', $content);
	}
	
	/**
	 * Clear the cache
	 * @return \Koldy\Redirect
	 * @link /report/clear-cache/12324324
	 */
	public function clearCacheAjax() {
		$id = Url::getVar(2);
		$cacheKey = "report-{$id}";
		Cache::delete($cacheKey);
		return Redirect::href('report', $id);
	}
	
	public function __call($method, $params) {
		return $this->viewAction();
	}
	
}