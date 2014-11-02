<?php

class OsVersionsController extends AbstractSessionController {
	
	public function indexAction() {
		$elements = array();
		
		$elements[] = Bootstrap::row()->add(12, Bootstrap::h(1, 'OS Versions'));
		
		$elements[] = BootstrapUI::tableRemote()
			->title('List of OS versions')
			->column('os', null, 100)
			->column('name', 'version')
			->column('total', 'count', 70)
			->column('action', '', 70)
			->sortableColumns(array('os', 'name', 'total'))
			->searchable()
			->sortField('name', 'asc');
		
		return View::create('base')
			->with('title', 'OS Versions')
			->with('content', $elements);
	}
	
	public function indexAjax() {
		return BootstrapUI::tableRemoteResponse()
			->search(array('os', 'name'))
			->column('os')
			->column('name')
			->column('total', function($value, $row) {
				return "<div class=\"text-right\">{$value}</div>";
			})
			->column('action', function($value, $row) {
				$findReports = \Bootstrap::anchor(\Bootstrap::icon('search'), \Koldy\Url::href('reports', 'search', array('os_version_id' => $row['id'])))
					->title('Find reports with this OS version')
					->asButton()
					->size('xs')
					->color('red');
				
				$priorityStackTraces = \Bootstrap::anchor(\Bootstrap::icon('zoom-in'), \Koldy\Url::href('os-versions', 'stack-traces', array('os_version_id' => $row['id'])))
					->title('Find most common stack traces')
					->asButton()
					->size('xs')
					->color('red');
				
				return "{$findReports} {$priorityStackTraces}";
			})
			->resultSet(Version::resultSet())
			->handle();
	}
	
	public function stackTracesAction() {
		$osVersionId = (int) Url::getVar('os_version_id');
		if ($osVersionId <= 0) {
			Application::throwError(400, 'Bad request');
		}
	
		$osVersion = Version::fetchOne($osVersionId);
		if ($osVersion === false) {
			Application::throwError(404, 'Can not find os_version');
		}
	
		$title = "Stack traces for {$osVersion->os} {$osVersion->name}";
		$elements = array();
	
		$elements[] = Bootstrap::row()->add(12, Bootstrap::h(1, $title)->secondaryText("Total {$osVersion->total} reports"));
	
		$elements[] = Bootstrap::row()->add(12, BootstrapUI::tableRemote()
			->title('Most common stack traces')
			->column('total', 'count', 80)
			->column('summary', 'stack trace summary')
			->column('action', '', 30)
			->sortableColumns(array('summary', 'total'))
			->sortField('total', 'desc')
			->extraParam('os_version_id', $osVersionId)
		);
	
		return View::create('base')
			->with('title', $title)
			->with('content', $elements);
	}
	
	public function stackTracesAjax() {
		$osVersionId = (int) Input::post('os_version_id');
		if ($osVersionId <= 0) {
			Application::throwError(400, 'Bad request');
		}
	
		$resultSet = new Stack\Trace\ResultSet\OsVersion();
		$resultSet->setOsVersionId($osVersionId);
	
		return BootstrapUI::tableRemoteResponse()
			->primaryKey('stack_trace_id')
		
			->column('total', function($value, $row) {
				return \Bootstrap::label($value)->color('red');
			})
			->column('summary', function($value, $row) {
				return "<pre class=\"text-danger\">{$value}</pre>";
			})
			->column('action', function($value, $row) use ($osVersionId) {
			return \Bootstrap::anchor(\Bootstrap::icon('search'), \Koldy\Url::href('reports', 'search', array('os_version_id' => $osVersionId, 'stack_trace_id' => $row['stack_trace_id'])))
				->title('Find reports with this os_version and package')
				->asButton()
				->size('xs')
				->color('red');
			})
			->resultSet($resultSet)
			->handle();
	}
}