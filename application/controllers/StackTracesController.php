<?php

use Koldy\Db\ResultSet;
use Koldy\Input;
class StackTracesController extends AbstractSessionController {
	
	public function indexAction() {
		$elements = array();
		
		$elements[] = Bootstrap::row()->add(12, Bootstrap::h(1, 'Stack traces')
			->secondaryText('This is list of collected stack trace summaries')
		);
		
		$table = BootstrapUI::tableRemote()
			->title('List of stack trace summaries')
			->searchable()
			->column('id')
			->column('summary')
			->column('total', 'count', 70)
			->sortableColumns(array('id', 'total'))
			->sortField('id', 'desc');
		
		$elements[] = $table;
		
		return View::create('base')
			->with('title', 'Stack traces')
			->with('content', $elements);
	}
	
	public function indexAjax() {
		return BootstrapUI::tableRemoteResponse()
			->search(array('summary'))
			->column('id', function($value, $row) {
				$user = \Session::get('user');
				$previousLogin = $user['previous_login'];
				
				return ($row['created_at'] >= $previousLogin)
					? ("{$value} " . \Bootstrap::label('NEW')->color('red'))
					: $value;
			})
			->column('summary', function($value, $row) {
				return "<pre class=\"text-danger\">{$value}</pre>";
			})
			->column('total', function($value, $row) {
				$search = \Bootstrap::anchor(\Bootstrap::icon('search'), \Koldy\Url::href('reports', 'search', array('stack_trace_id' => $row['id'])))
					->title('Find reports with this stack trace')
					->asButton()
					->size('xs')
					->color('red');
				
				$open = \BootstrapUI::buttonRemote(\Bootstrap::icon('eye-open'))
					->progressText(\Bootstrap::icon('zoom-in'))
					->param('stack_trace_id', $row['id'])
					->url(\Koldy\Url::href('stack-traces', 'find-any'))
					->size('xs')
					->color('green');
				return "<p class=\"text-right\">{$value}</p>{$search} {$open}";
			})
			->resultSet(Stack\Trace::resultSet())
			->handle();
	}
	
	public function findAnyAjax() {
		$stackTraceId = Input::post('stack_trace_id');
		
		$total = Crash\Archive::count(array('stack_trace_id' => $stackTraceId));
		if ($total > 0) {
			$random = rand(0, $total -1);
			$records = Crash\Archive::query()->field('id')->where('stack_trace_id', $stackTraceId)->limit($random, 1)->fetchAllObj();
			$id = $records[0]->id;
			return BootstrapUI::buttonRemoteResponse()
				->redirect(\Koldy\Url::href('report', $id));
		} else {
			return BootstrapUI::buttonRemoteResponse()
				->disableButton()
				->text('No reports');
		}
	}
}