<?php

class ProvidersController extends AbstractSessionController {
	
	public function indexAction() {
		$elements = array();
		
		$elements[] = Bootstrap::row()->add(12, Bootstrap::h(1, 'Internet providers'));
		
		$elements[] = BootstrapUI::tableRemote()
			->title('List of providers')
			->column('name', 'provider name')
			->column('total', 'count', 70)
			->column('action', '', 30)
			->sortableColumns(array('name', 'total'))
			->searchable()
			->sortField('name', 'asc');
		
		return View::create('base')
			->with('title', 'Internet providers')
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
				return \Bootstrap::anchor(\Bootstrap::icon('search'), \Koldy\Url::href('reports', 'search', array('provider_id' => $row['id'])))
					->title('Find reports sent from this provider')
					->asButton()
					->size('xs')
					->color('red');
			})
			->resultSet(Provider::resultSet())
			->handle();
	}
}