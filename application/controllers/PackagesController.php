<?php

use Koldy\Url;
use Koldy\Application;
use Koldy\Input;

class PackagesController extends AbstractSessionController {
	
	public function indexAction() {
		$elements = array();
		
		$elements[] = Bootstrap::row()->add(12, Bootstrap::h(1, 'Packages'));
		
		$elements[] = BootstrapUI::tableRemote()
			->title('List of packages')
			->column('name', 'package name')
			->column('total', 'count', 70)
			->column('action', '', 100)
			->sortableColumns(array('name', 'total'))
			->searchable()
			->sortField('name', 'asc');
		
		return View::create('base')
			->with('title', 'Packages')
			->with('content', $elements);
	}
	
	public function indexAjax() {
		return BootstrapUI::tableRemoteResponse()
			->search(array('name'))
			->column('name')
			->column('total', function($value, $row) {
				return "<div class=\"text-right\">{$value}</div>";
			})
			->column('action', function($value, $row) {
				$findReports = \Bootstrap::anchor(\Bootstrap::icon('search'), \Koldy\Url::href('reports', 'search', array('package_id' => $row['id'])))
					->title('Find reports with this package')
					->asButton()
					->size('xs')
					->color('red');
				
				$priorityStackTraces = \Bootstrap::anchor(\Bootstrap::icon('zoom-in'), \Koldy\Url::href('packages', 'stack-traces', array('package_id' => $row['id'], 'last' => 'week')))
					->title('Find most common stack traces')
					->asButton()
					->size('xs')
					->color('red');
				
				$packageVersions = \Bootstrap::anchor(\Bootstrap::icon('filter'), \Koldy\Url::href('packages', 'versions', array('package_id' => $row['id'], 'last' => 'week')))
					->title('Show versions of this package')
					->asButton()
					->size('xs')
					->color('blue');
				
				return "{$findReports} {$priorityStackTraces} {$packageVersions}";
			})
			->resultSet(Package::resultSet())
			->handle();
	}
	
	public function stackTracesAction() {
		$packageId = (int) Url::getVar('package_id');
		if ($packageId <= 0) {
			Application::throwError(400, 'Bad request');
		}
		
		$package = Package::fetchOne($packageId);
		if ($package === false) {
			Application::throwError(404, 'Can not find package');
		}
		
		$last = Url::getVar('last');
		
		$title = 'Stack traces for ' . $package->name;
		$elements = array();
		
		$resultSet = new Stack\Trace\ResultSet\Package();
		$resultSet->setPackageId($packageId, $last);
		$total = $resultSet->count();
		
		$elements[] = Bootstrap::row()->add(12, Bootstrap::h(1, $title)->secondaryText("Total {$total} errors"));
		
		$elements[] = Bootstrap::row()->add(12, Bootstrap::buttonGroup()
				->add(Bootstrap::anchor('Last 12 hours', Url::href('packages', 'stack-traces', array('package_id' => $packageId, 'last' => '12-hour')))->asButton()->color($last == '12-hour' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('Last day', Url::href('packages', 'stack-traces', array('package_id' => $packageId, 'last' => 'day')))->asButton()->color($last == 'day' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('Last week', Url::href('packages', 'stack-traces', array('package_id' => $packageId, 'last' => 'week')))->asButton()->color($last == 'week' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('Last 2 weeks', Url::href('packages', 'stack-traces', array('package_id' => $packageId, 'last' => '2-weeks')))->asButton()->color($last == '2-weeks' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('Last month', Url::href('packages', 'stack-traces', array('package_id' => $packageId, 'last' => 'month')))->asButton()->color($last == 'month' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('Last 2 months', Url::href('packages', 'stack-traces', array('package_id' => $packageId, 'last' => '2-months')))->asButton()->color($last == '2-months' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('All', Url::href('packages', 'stack-traces', array('package_id' => $packageId)))->asButton()->color($last == null ? 'blue' : 'default'))
			->setAttribute('style', 'margin-bottom: 10px')
		);
	
		$elements[] = BootstrapUI::tableRemote()
			->title('Most common stack traces')
			->column('total', 'count', 80)
			->column('summary', 'stack trace summary')
			->column('action', '', 30)
			->sortableColumns(array('summary', 'total'))
			->sortField('total', 'desc')
			->extraParam('package_id', $packageId)
			->extraParam('last', $last);
	
		return View::create('base')
		->with('title', $title)
		->with('content', $elements);
	}
	
	public function stackTracesAjax() {
		$packageId = (int) Input::post('package_id');
		if ($packageId <= 0) {
			Application::throwError(400, 'Bad request');
		}
		
		$resultSet = new Stack\Trace\ResultSet\Package();
		$resultSet->setPackageId($packageId, Input::post('last'));
		
		return BootstrapUI::tableRemoteResponse()
			->primaryKey('stack_trace_id')
			
			->column('total', function($value, $row) {
				return \Bootstrap::label($value)->color('red');
			})
			->column('summary', function($value, $row) {
				return "<pre class=\"text-danger\">{$value}</pre>";
			})
			->column('action', function($value, $row) use ($packageId) {
				return \Bootstrap::anchor(\Bootstrap::icon('search'), \Koldy\Url::href('reports', 'search', array('package_id' => $packageId, 'stack_trace_id' => $row['stack_trace_id'])))
					->title('Find reports with this stack trace and package')
					->asButton()
					->size('xs')
					->color('red');
			})
			->resultSet($resultSet)
			->handle();
	}

	public function versionsAction() {
		$packageId = (int) Url::getVar('package_id');
		if ($packageId <= 0) {
			Application::throwError(400, 'Bad request');
		}
	
		$package = Package::fetchOne($packageId);
		if ($package === false) {
			Application::throwError(404, 'Can not find package');
		}
	
		$last = Url::getVar('last');
	
		$title = 'Package versions for ' . $package->name;
		$elements = array();
	
		$elements[] = Bootstrap::row()->add(12, Bootstrap::h(1, $title));
	
		$elements[] = Bootstrap::row()->add(12, Bootstrap::buttonGroup()
				->add(Bootstrap::anchor('Last 12 hours', Url::href('packages', 'versions', array('package_id' => $packageId, 'last' => '12-hour')))->asButton()->color($last == '12-hour' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('Last day', Url::href('packages', 'versions', array('package_id' => $packageId, 'last' => 'day')))->asButton()->color($last == 'day' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('Last week', Url::href('packages', 'versions', array('package_id' => $packageId, 'last' => 'week')))->asButton()->color($last == 'week' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('Last 2 weeks', Url::href('packages', 'versions', array('package_id' => $packageId, 'last' => '2-weeks')))->asButton()->color($last == '2-weeks' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('Last month', Url::href('packages', 'versions', array('package_id' => $packageId, 'last' => 'month')))->asButton()->color($last == 'month' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('Last 2 months', Url::href('packages', 'versions', array('package_id' => $packageId, 'last' => '2-months')))->asButton()->color($last == '2-months' ? 'blue' : 'default'))
				->add(Bootstrap::anchor('All', Url::href('packages', 'versions', array('package_id' => $packageId)))->asButton()->color($last == null ? 'blue' : 'default'))
			->setAttribute('style', 'margin-bottom: 10px')
		);
	
		$elements[] = BootstrapUI::tableRemote()
			->title('Most common versions')
			->column('total', 'count', 80)
			->column('name', 'package version')
			->column('action', '', 30)
			->sortableColumns(array('name', 'total'))
			->sortField('total', 'desc')
			->extraParam('package_id', $packageId)
			->extraParam('last', $last);
	
		return View::create('base')
			->with('title', $title)
			->with('content', $elements);
	}

	public function versionsAjax() {
		$packageId = (int) Input::post('package_id');
		if ($packageId <= 0) {
			Application::throwError(400, 'Bad request');
		}
	
		$resultSet = new Package\ResultSet\Version();
		$resultSet->setPackageId($packageId, Input::post('last'));
		$timeFrom = $resultSet->getFromTime();
	
		return BootstrapUI::tableRemoteResponse()
			->primaryKey('package_version_id')
			
			->column('total', function($value, $row) {
				return \Bootstrap::label($value)->color('red');
			})
			->column('name')
			->column('action', function($value, $row) use ($packageId, $timeFrom) {
				return \Bootstrap::anchor(\Bootstrap::icon('search'), \Koldy\Url::href('reports', 'search', array('package_version_id' => $row['package_version_id'], 'date_from' => \Misc::userDate('Y-m-d H:i:s', strtotime($timeFrom)))))
					->title('Find reports with this package version')
					->asButton()
					->size('xs')
					->color('red');
			})
			->resultSet($resultSet)
			->handle();
	}
}