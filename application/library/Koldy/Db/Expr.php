<?php namespace Koldy\Db;
/**
 * The expression data holder - string stored in this class will be
 * literaly printed in query with no additional adding slashes or anything
 * like that.
 *
 */
class Expr {


	/**
	 * @var string
	 */
	private $data = null;


	/**
	 * Construct the object
	 * @param string $data
	 */
	public function __construct($data) {
		$this->data = $data;
	}


	/**
	 * Get the data
	 * @return string
	 */
	public function getData() {
		return $this->data;
	}


	/**
	 * Print the data as is
	 * @return string
	 */
	public function __toString() {
		$data = $this->getData();
		return ($data !== null ? $data : '');
	}

}
