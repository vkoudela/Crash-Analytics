<?php

class BrandsController extends AbstractSessionController {
	
	public function indexAction() {
		$elements = array();
		
		$elements[] = Bootstrap::row()->add(12, Bootstrap::h(1, 'Brands'));
		
		$elements[] = BootstrapUI::tableRemote()
			->title('List of brand names')
			->column('name', 'brand name')
			->column('total', 'count', 70)
			->column('action', '', 70)
			->sortableColumns(array('name', 'total'))
			->searchable()
			->sortField('name', 'asc');
		
		return View::create('base')
			->with('title', 'Brands')
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
				$findReports = \Bootstrap::anchor(\Bootstrap::icon('search'), \Koldy\Url::href('reports', 'search', array('brand_id' => $row['id'])))
					->title('Find reports with this brand')
					->asButton()
					->size('xs')
					->color('red');
				
				$priorityStackTraces = \Bootstrap::anchor(\Bootstrap::icon('zoom-in'), \Koldy\Url::href('brands', 'stack-traces', array('brand_id' => $row['id'])))
					->title('Find most common stack traces')
					->asButton()
					->size('xs')
					->color('red');
				
				return "{$findReports} {$priorityStackTraces}";
			})
			->resultSet(Brand::resultSet())
			->handle();
	}
	
	public function stackTracesAction() {
		$brandId = (int) Url::getVar('brand_id');
		if ($brandId <= 0) {
			Application::throwError(400, 'Bad request');
		}
	
		$brand = Brand::fetchOne($brandId);
		if ($brand === false) {
			Application::throwError(404, 'Can not find brand');
		}
	
		$title = 'Stack traces for ' . $brand->name;
		$elements = array();
	
		$elements[] = Bootstrap::row()->add(12, Bootstrap::h(1, $title)->secondaryText("Total {$brand->total} reports"));
	
		$elements[] = Bootstrap::row()->add(12, BootstrapUI::tableRemote()
			->title('Most common stack traces')
			->column('total', 'count', 80)
			->column('summary', 'stack trace summary')
			->column('action', '', 30)
			->sortableColumns(array('summary', 'total'))
			->sortField('total', 'desc')
			->extraParam('brand_id', $brandId)
		);
	
		return View::create('base')
			->with('title', $title)
			->with('content', $elements);
	}
	
	public function stackTracesAjax() {
		$brandId = (int) Input::post('brand_id');
		if ($brandId <= 0) {
			Application::throwError(400, 'Bad request');
		}
	
		$resultSet = new Stack\Trace\ResultSet\Brand();
		$resultSet->setBrandId($brandId);
	
		return BootstrapUI::tableRemoteResponse()
			->primaryKey('stack_trace_id')
				
			->column('total', function($value, $row) {
				return \Bootstrap::label($value)->color('red');
			})
			->column('summary', function($value, $row) {
				return "<pre class=\"text-danger\">{$value}</pre>";
			})
			->column('action', function($value, $row) use ($brandId) {
				return \Bootstrap::anchor(\Bootstrap::icon('search'), \Koldy\Url::href('reports', 'search', array('brand_id' => $brandId, 'stack_trace_id' => $row['stack_trace_id'])))
					->title('Find reports with this brand and package')
					->asButton()
					->size('xs')
					->color('red');
			})
			->resultSet($resultSet)
			->handle();
	}
}