<?php namespace BootstrapUI\Table;

use Bootstrap\Panel;
use Bootstrap\Table;
use \Koldy\Json;

class Remote extends \Bootstrap\Table {
	
	/**
	 * The URL of data source
	 * @var string
	 */
	protected $url = null;

	/**
	 * Extra parameters that will be sent to data source
	 * @var array
	 */
	protected $extraParams = array();

	/**
	 * The table object
	 * @var \Bootstrap\Table
	 */
	protected $table = null;

	/**
	 * The Panel object
	 * @var \Bootstrap\Panel
	 */
	protected $panel = null;

	/**
	 * Table start page
	 * @var int
	 */
	protected $startPage = 1;
	
	/**
	 * Initial sort by field
	 * @var string
	 */
	protected $sortField = null;

	/**
	 * Initial sort by direction
	 * @var string
	 */
	protected $sortDirection = 'asc';

	/**
	 * Array of sortable columns
	 * @var array
	 */
	protected $sortableColumns = array();
	
	/**
	 * Is panel collapsible and what is initial state
	 * @var bool
	 */
	protected $collapsible = null;
	
	/**
	 * Is search enabled or not. By default, it isn't
	 * @var bool
	 */
	protected $searchEnabled = false;

	/**
	 * Construct the object
	 * @param string $url
	 */
	public function __construct($url = null) {
		$this->url = $url;

		$this->panel = new Panel();
		$this->panel->footer('');
		$this->panel->addClass('x-panel-remote');

		$this->table = new Table();
		$this->table->addClass('x-table-remote');
	}

	/**
	 * Get the table object
	 * @return \Bootstrap\Table
	 */
	public function table() {
		return $this->table;
	}

	/**
	 * Get the panel object
	 * @return \Bootstrap\Panel
	 */
	public function panel() {
		return $this->panel;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::id()
	 */
	public function id($id) {
		$this->table->id($id);
		return $this;
	}

	/**
	 * Add the column definition
	 * @param string $index must match column name from query result
	 * @param string $label [optional]
	 * @param string $width [optional]
	 * @param string $css [optional]
	 * @return \BootstrapUI\Table\Remote
	 */
	public function column($index, $label = null, $width = null, $css = null) {
		if ($label === null) {
			$label = str_replace('_', ' ', $index);
		}
		$this->table->column($index, $label, $width, $css);
		return $this;
	}

	/**
	 * Set the panel title
	 * @param string $title
	 * @return \BootstrapUI\Table\Remote
	 */
	public function title($title) {
		$this->panel->title($title);
		return $this;
	}
	
	/**
	 * How to render certain cell
	 * @param string $index
	 * @param function $function
	 * @return \BootstrapUI\Table\Remote
	 */
	public function render($index, $function) {
		$this->table->render($index, $function);
		return $this;
	}

	/**
	 * Add the value renderer
	 * @param string $index the column key
	 * @param function(mixed $value, array $row) $function
	 * @return \Bootstrap\Table\Remote
	 */
	public function renderTd($index, $function) {
		$this->table->renderTd($index, $function);
		return $this;
	}

	/**
	 * How to render table head cell
	 * @param string $index
	 * @param function $function
	 * @return \BootstrapUI\Table\Remote
	 */
	public function renderTh($index, $function) {
		$this->table->renderTh($index, $function);
		return $this;
	}
	
	/**
	 * Is this table searchable or not
	 * @param string $enable
	 * @return \BootstrapUI\Table\Remote
	 */
	public function searchable($enable = true) {
		$this->searchEnabled = $enable;
		return $this;
	}
	
	/**
	 * Is this panel collapsible or not
	 * @param string $collapsed
	 * @return \BootstrapUI\Table\Remote
	 */
	public function collapsible($collapsed = true) {
		$this->collapsible = $collapsed;
		return $this;
	}

	/**
	 * Which are the sortable columns
	 * @param array $sortableColumns
	 * @return \BootstrapUI\Table\Remote
	 */
	public function sortableColumns(array $sortableColumns) {
		$this->sortableColumns = $sortableColumns;

		foreach ($sortableColumns as $index) {
			$this->table->renderTh($index, function($index, $column, $columns) {
				$dataLabel = str_replace('"', '&quot;', $column['label']);
				$width = ($column['width'] !== null ? " style=\"width:{$column['width']}px\"" : '');
				return "<th class=\"x-remote-sortable-column\"{$width}><a href=\"#\" class=\"x-remote-sortable-{$index}\" data-field=\"{$index}\" data-label=\"{$dataLabel}\">{$column['label']}</a></th>";
			});
		}

		return $this;
	}
	
	/**
	 * Add extra parameter that will be sent to data source
	 * @param string $key
	 * @param string $value
	 * @return \BootstrapUI\Table\Remote
	 */
	public function extraParam($key, $value) {
		$this->extraParams[$key] = $value;
		return $this;
	}

	/**
	 * Set the array of extra params.
	 * Please do not use this params names: page, limit, field, dir - those params will be overriden in Javascript
	 * @param array $extraParams
	 * @return Remote
	 */
	public function extraParams(array $extraParams) {
		$this->extraParams = $extraParams;
		return $this;
	}

	/**
	 * Set the start page
	 * @param string $startFromPage
	 * @return \BootstrapUI\Table\Remote
	 */
	public function startPage($startFromPage) {
		$this->startPage = (int) $startFromPage;
		return $this;
	}

	/**
	 * Set the initial field that the data will be sorted by
	 * @param string $sortField
	 * @param string $sortDirection 'asc' or 'desc'
	 * @return \BootstrapUI\Table\Remote
	 */
	public function sortField($sortField, $sortDirection = null) {
		$this->sortField = $sortField;

		if ($sortDirection !== null) {
			$this->sortDirection($sortDirection);
		}

		return $this;
	}

	/**
	 * Set the sort direction
	 * @param string $dir asc or desc
	 */
	public function sortDirection($dir) {
		$this->sortDirection = strtolower($dir);
		return $this;
	}
	
	/**
	 * URL of where is the data source
	 * @param string $url
	 * @return \BootstrapUI\Table\Remote
	 */
	public function url($url) {
		$this->url = $url;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		if ($this->searchEnabled) {
			$search =
			'<form class="form-inline x-remote-search-form" role="form">' .
				'<div class="form-group">' .
					'<label class="sr-only" for="search">Search</label>' . 
    				'<input type="search" class="form-control input-sm animate-width" id="search" placeholder="Search" style="width:80px;">' .
    				'<button type="reset" class="btn btn-xs btn-link">' . \Bootstrap::icon('remove-circle') . '</button>' .
				'</div>' . 
			'</form>';
			
			$this->panel->addHeaderElement($search);
		}
		
		if ($this->collapsible !== null) {
			$this->panel->collapsible($this->collapsible);
		}
		
		$footer =
		'<div class="row">' .
			'<div class="col-md-6 x-remote-table-info"></div>' .
			'<div class="col-md-6 x-remote-table-pagination text-right"></div>' .
		'</div>';
		$this->panel->footer($footer);

		$this->table->data('url', ($this->url === null) ? $_SERVER['REQUEST_URI'] : $this->url);
		$this->table->data('page', $this->startPage);
		$this->table->data('limit', 10);
		$this->table->data('field', $this->sortField === null ? $this->table->getPrimaryKey() : $this->sortField);
		$this->table->data('dir', $this->sortDirection);
		$this->table->data('extra-params', base64_encode(Json::encode($this->extraParams)));

		$this->panel->content($this->table);
		return $this->panel->getHtml();
	}
}