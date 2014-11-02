<?php namespace Bootstrap;

use Koldy\Db\Model;
use Koldy\Log;

/**
 * Static table.
 * @author vkoudela
 * @link http://getbootstrap.com/css/#tables
 *
 */
class Table extends HtmlElement {

	/**
	 * Array of columns
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Data per rows per columns that will be rendered into cells
	 * @var array
	 */
	protected $data = array();

	/**
	 * Primary key in each row for special actions
	 * @var string
	 */
	protected $primaryKey = 'id';

	/**
	 * Custom cell renderers per column
	 * @var array of functions with column index as key
	 */
	protected $renderers = array();

	/**
	 * Custom cell renderers for table head
	 * @var array of functions
	 */
	protected $renderersTd = array();

	/**
	 * Add column definition
	 * @param string $index
	 * @param string $label
	 * @param string $width
	 * @param string $css
	 * @return \Bootstrap\Table
	 */
	public function column($index, $label = null, $width = null, $css = null) {
		$this->columns[$index] = array(
			'label' => ($label === null ? $index : $label),
			'width' => $width,
			'css' => $css
		);
		return $this;
	}

	/**
	 * Get the column definitions
	 * @return array
	 */
	public function getColumns() {
		return $this->columns;
	}

	/**
	 * Add row
	 * @param array $row
	 * @return \Bootstrap\Table
	 */
	public function row($row) {
		$this->data[] = $row;
		return $this;
	}

	/**
	 * Add multiple rows
	 * @param array $rows
	 * @return \Bootstrap\Table
	 */
	public function rows(array $rows) {
		foreach ($rows as $row) {
			$this->row($row);
		}
		return $this;
	}

	/**
	 * Delete all data from dataset
	 * @return \Bootstrap\Table
	 */
	public function deleteAll() {
		$this->data = array();
		return $this;
	}

	/**
	 * Set the primary key in table
	 * @param string $pk
	 * @return \Bootstrap\Table
	 */
	public function primaryKey($pk) {
		$this->primaryKey = $pk;
		return $this;
	}

	/**
	 * Get the primary key
	 * @return string
	 */
	public function getPrimaryKey() {
		return $this->primaryKey;
	}

	/**
	 * Add the custom function as cell renderer
	 * @param string $index the column key
	 * @param function(mixed $value, array $rowValues) $function
	 * @return \Bootstrap\Table
	 */
	public function render($index, $function) {
		$this->renderers[$index] = $function;
		return $this;
	}

	/**
	 * Get cell renderers
	 * @return array
	 */
	public function getRenderers() {
		return $this->renderers;
	}

	/**
	 * Add renderer for complete cell in table
	 * @param string $index
	 * @param function(mixed $value, array $rowValues) $function must return complete <td></td>
	 * @return \Bootstrap\Table
	 */
	public function renderTd($index, $function) {
		$this->renderersTd[$index] = $function;
		return $this;
	}

	/**
	 * Add the TH renderer. You must return the complete part: <th>something</th>
	 * @param string $index the column key
	 * @param function($index, array $column, array $otherColumns) $function - the $column contains ['label', 'width']
	 * @return Table
	 */
	public function renderTh($index, $function) {
		$this->renderersTh[$index] = $function;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		$html = "<table{$this->classAttr('table table-striped table-hover')}{$this->idAttr()}{$this->getAttributes()}>\n";

		$html .= "<thead>\n\t<tr>\n";
		foreach ($this->columns as $index => $column) {
			if (isset($this->renderersTh[$index])) {
				$fn = $this->renderersTh[$index];
				$html .= $fn($index, $column, $this->columns);
			} else {
				$width = ($column['width'] !== null ? " style=\"width:{$column['width']}px\"" : '');
				$html .= "\t\t<th{$width}>{$column['label']}</th>\n";
			}
		}
		$html .= "\t</tr>\n</thead>\n<tbody>\n";

		foreach ($this->data as $row) {
			// TODO: Remove Koldy sepcific code here "Model"
			if ((is_array($row) && !isset($row[$this->primaryKey]))
				|| (is_object($row) && !($row instanceof Model) && !property_exists($row, $this->primaryKey))
				|| ($row instanceof Model && !$row->has($this->primaryKey)))
			{
				throw new Exception("Can not render " . get_class($this) . " because primary field={$this->primaryKey} value doesn't exists: " . print_r($row, true));
			} else {
				if (is_array($row)) {
					$idValue = $row[$this->primaryKey];
				} else {
					$pk = $this->primaryKey;
					$idValue = $row->$pk;
				}
				
				$html .= "\t<tr id=\"{$this->getId()}_{$idValue}\" data-id=\"{$idValue}\">\n";
				foreach ($this->columns as $index => $column) {
					if (isset($this->renderersTd[$index])) {
						$fn = $this->renderersTd[$index];
						$html .= $fn($row);
					} else {
						$add = ($column['css'] !== null) ? " class=\"{$column['css']}\"" : '';
						$html .= "\t\t<td{$add}>";
				
						if (isset($this->renderers[$index])) {
							$fn = $this->renderers[$index];
							$html .= $fn(is_object($row) ? $row->$index : $row[$index], $row);
						} else if (is_array($row) && isset($row[$index])) {
							$html .= $row[$index];
						} else if ($row instanceof Model && $row->has($index)) {
							$html .= $row->$index;
						} else if (is_object($row) && property_exists($row, $index)) {
							$html .= $row->$index;
						}
						$html .= "</td>\n";
					}
				}
				$html .= "\t</tr>\n";
			}
		}

		$html .= "</tbody>\n";
		$html .= "</table>\n";

		return $html;
	}

}
