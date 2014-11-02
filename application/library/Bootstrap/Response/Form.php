<?php namespace Bootstrap\Response;

class Form extends AbstractResponse {

	protected $fieldErrors = array();
	
	protected $fieldValues = array();

	public function __construct() {
		parent::__construct();
		$this->set('ok', true);
		$this->icon('ok', 'green');
	}
	
	public function message($helpText) {
		$this->set('helpText', $helpText);
		return $this;
	}

	public function failed($message = null) {
		$this->set('ok', false);
		if ($message !== null) {
			$this->message($message);
		}
		return $this->icon('remove', 'red');
	}
	
	/**
	 * Failed on given array of fields
	 * @param mixed $fields assoc array with structure (field => error message)
	 * @return \Bootstrap\Response\Form
	 */
	public function failedOn($fields) {
		if ($fields instanceof \Koldy\Validator) {
			$fields = $fields->getMessages();
		}
		
		foreach ($fields as $field => $message) {
			$this->failedOnField($field, $message);
		}
		
		return $this;
	}

	/**
	 * Bind error message on one field
	 * @param string $field
	 * @param string $message [optional]
	 * @param string $state [optional]
	 * @return \Bootstrap\Response\Form
	 */
	public function failedOnField($field, $message = null, $state = 'error') {
		$this->set('ok', false);
		$this->fieldErrors[$field] = array(
			'message' => $message,
			'state' => $state
		);
		return $this->icon('remove', 'red');
	}
	
	/**
	 * Update field's value on response
	 * @param string $field
	 * @param mixed $value
	 * @return \Bootstrap\Response\Form
	 */
	public function fieldValue($field, $value) {
		$this->fieldValues[$field] = $value;
		return $this;
	}
	
	/**
	 * Update field values on response
	 * @param array $fieldValues
	 * @return \Bootstrap\Response\Form
	 */
	public function fieldValues(array $fieldValues) {
		$this->fieldValues = $fieldValues;
		return $this;
	}
	
	/**
	 * Set the icon to the form
	 * @param string $icon only the icon name; @see Boostrap::icon
	 * @param string $color [optional]
	 * @return \Bootstrap\Response\Form
	 */
	public function icon($icon, $color = null) {
		$this->set('icon', \Bootstrap::icon($icon, $color));
		return $this;
	}

	/**
	 * Mark the field as invalid, set the message and state
	 * @param  string $field the field name
	 * @param  string $message the message under the field
	 * @param  string $state error, warning or success
	 * @return  \Bootstrap\Response\Form
	 */
	public function invalid($field, $message, $state = 'error') {
		$this->fieldErrors[$field] = array(
			'message' => $message,
			'state' => $state
		);

		return $this;
	}

	public function markField($field, $state = 'error') {
		$this->fieldErrors[$field] = array('state' => $state);
		return $this;
	}

	/**
	 * This will trigger the page refresh on the client side
	 * @return  \Bootstrap\Response\Form
	 */
	public function refresh() {
		$this->set('refresh', true);
		return $this;
	}
	
	/**
	 * Trigger table refresh that is currently presented on the page
	 * @param string $tableId
	 * @return \Bootstrap\Response\Form
	 */
	public function refreshTable($tableId) {
		$this->set('refreshTable', $tableId);
		return $this;
	}

	/**
	 * This will redirect user to the given address
	 * @param  string $where
	 * @return  \Bootstrap\Response\Form
	 */
	public function redirect($where) {
		$this->set('redirect', $where);
		return $this;
	}

	/**
	 * This will redirect user back, but only if referrer information exists
	 * @return  \Bootstrap\Response\Form
	 */
	public function redirectBack() {
		$this->set('back', 'yes');
		return $this;
	}

	public function flush() {
		if (sizeof($this->fieldValues) > 0) {
			$this->set('fieldValues', $this->fieldValues);
		}
		
		if (sizeof($this->fieldErrors) > 0) {
			$this->set('fieldErrors', $this->fieldErrors);
		}
		parent::flush();
	}
}