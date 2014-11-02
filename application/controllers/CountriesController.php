<?php

class CountriesController extends AbstractSessionController {
	
	public function indexAction() {
		$elements = array();
		
		$elements[] = Bootstrap::row()->add(12, Bootstrap::h(1, 'Countries'));
		
		$elements[] = BootstrapUI::tableRemote()
			->title('List of countries')
			->column('flag', '', 30)
			->column('tld', '', 30)
			->column('country')
			->column('total', 'count', 70)
			->column('action', '', 30)
			->sortableColumns(array('tld', 'country', 'total'))
			->searchable()
			->sortField('country', 'asc');
		
		return View::create('base')
			->with('title', 'Countries')
			->with('content', $elements);
	}
	
	public function indexAjax() {
		return BootstrapUI::tableRemoteResponse()
			->search(array('country'))
			->column('flag', function($value, $row) {
				return '<img src="' . \Koldy\Url::link('img/flag/' . $row['tld'] . '.png') . '" />';
			})
			->column('tld')
			->column('country')
			->column('total', function($value, $row) {
				return "<div class=\"text-right\">{$value}</div>";
			})
			->column('action', function($value, $row) {
				return \Bootstrap::anchor(\Bootstrap::icon('search'), \Koldy\Url::href('reports', 'search', array('country_id' => $row['id'])))
					->title('Find reports from this country')
					->asButton()
					->size('xs')
					->color('red');
			})
			->resultSet(Country::resultSet())
			->handle();
	}
}