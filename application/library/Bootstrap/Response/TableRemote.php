<?php namespace Bootstrap\Response;

use Koldy\Input;

/**
 * @deprecated
 */
class TableRemote extends AbstractResponse {
	
	/**
	 * @var \Koldy\Db\ResultSet
	 */
	private $resultSet = null;

	private $columns = array();

	private $columnValueModifier = array();

	private $primaryKey = 'id';

	private $params = null;
	
	private $data = null;
	
	private $tbody = null;
	
	private $searchFields = null;
	
	/**
	 * @var \Koldy\Pagination
	 */
	private $pagination = null;
	
	public function resultSet(\Koldy\Db\ResultSet $resultSet) {
		$this->resultSet = $resultSet;
		return $this;
	}

	public function useTable(\Bootstrap\Table\Response $remoteTable) {
		$renderers = $remoteTable->table()->getRenderers();
		foreach ($remoteTable->table()->getColumns() as $column => $properties) {
			// TODO copy table from remoteTable definition to this response
		}
		return $this;
	}

	/**
	 * Add the column in response for rendering rows
	 * @param string $name
	 * @param function(mixed $cellValue, array $row) $valueModifierFunction
	 * @return \Bootstrap\Response\TableRemote
	 */
	public function column($name, $valueModifierFunction = null) {
		$this->columns[] = $name;

		if ($valueModifierFunction !== null) {
			$this->columnValueModifier[$name] = $valueModifierFunction;
		}

		return $this;
	}
	
	public function search(array $whatFields) {
		$this->searchFields = $whatFields;
		return $this;
	}

	public function primaryKey($primaryKey) {
		$this->primaryKey = $primaryKey;
		return $this;
	}
	
	/**
	 * Get the pagination object. This will be initiated only after calling the handle() method
	 * @return \Koldy\Pagination
	 */
	public function getPagination() {
		return $this->pagination;
	}
	
	/**
	 * Handle the request
	 * @return \Bootstrap\Response\TableRemote
	 */
	public function handle() {
		$this->params = Input::requireParams('page', 'limit', 'field', 'dir');
		
		$page = (int) $this->params->page;
		if ($page < 1) {
			$page = 1;
		}
		
		$limitPerPage = (int) $this->params->limit;
		$field = (string) trim($this->params->field);
		
		$this->resultSet
			->page($page, $limitPerPage)
			->orderBy($field, $this->params->dir);
		
		// check if this is request with search
		$search = trim((string) \Koldy\Input::post('search'));
		if ($search != '') {
			$fields = ($this->searchFields !== null) ? $this->searchFields : $this->columns;
			foreach ($fields as $field) {
				$this->resultSet->orWhere($field, "%{$search}%", null, 'LIKE');
			}
		}
		
		\Log::debug($this->resultSet);
		
		// get the rows of data
		$this->data = $this->resultSet->fetch();
		
		if (is_array($this->data)) {
			$this->tbody = array();
			foreach ($this->data as $row) {
				$this->tbody[] = "<tr data-id=\"{$row[$this->primaryKey]}\">";
				foreach ($this->columns as $column) {
					$value = isset($row[$column]) ? $row[$column] : null;
					if (isset($this->columnValueModifier[$column])) {
						$fn = $this->columnValueModifier[$column];
						$fnResult = $fn($value, $row);
						$this->tbody[] = "<td>{$fnResult}</td>";
					} else {
						$value = strip_tags(stripslashes($value));
						$this->tbody[] = "<td>{$value}</td>";
					}
				}
				$this->tbody[] = '</tr>';
			}
			
			$this->tbody = implode("\n", $this->tbody);
		}
		
		// get the totals
		$total = (int) $this->resultSet->count();
		
		// get the info
		if ($total > 0) {
			$start = ($this->params->page -1) * $this->params->limit +1;
			$end = $start + $this->params->limit;
			if ($end > $total) {
				$end = $total;
			}
		
			/*$this->info = __('table.records.info', 'Showing {from} - {to} of {total}',
				'The info below remote table about how many rows are visible. Variables are {from}, {to} and {total}',
				array('from' => $start, 'to' => $end, 'total' => $total)
			);*/
			$this->info = "Showing {$start} - {$end} of {$total}";
		} else {
			$this->info = 'No results';
		}
		
		// create pagination
		$this->pagination = new \Koldy\Pagination($this->params->page, $total);
		$this->pagination->setItemsPerPage(10)
			->setCssDefault('btn btn-primary btn-xs')
			->setCssSelected('btn-info');
		
		return $this;
	}
	
	public function flush() {
		$this->set('data', $this->tbody);
		$this->set('info', $this->info);
		$this->set('pagination', $this->pagination->generate());

		parent::flush();
	}
}