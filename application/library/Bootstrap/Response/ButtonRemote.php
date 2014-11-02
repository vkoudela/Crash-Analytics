<?php namespace Bootstrap\Response;

use Bootstrap\HtmlElement;
class ButtonRemote extends AbstractResponse {
	
	protected $params = array();
	
	public function failed($message = null) {
		$this->set('success', false);
		if ($message !== null) {
			$this->set('message', $message);
		}
		return $this;
	}
	
	/**
	 * Removes the table's row where button is rendered
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function removeParentRow() {
		return $this->removeClosest('tr');
	}
	
	/**
	 * Remove the closest HTML element
	 * @param string $cssSelector
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function removeClosest($cssSelector) {
		$this->set('removeClosest', $cssSelector);
		return $this;
	}
	
	public function removeButton() {
		$this->set('removeButton', 'yes');
		return $this;
	}
	
	/**
	 * Removes all buttons that are in the same level where this button is rendered
	 * except of themself. If you want to remove also this button, then please call removeButton() method.
	 */
	public function removeOtherButtons() {
		$this->set('removeOtherButtons', 'yes');
		return $this;
	}
	
	public function disableButton() {
		$this->set('disabled', 'yes');
		return $this;
	}
	
	public function disableOtherButtons() {
		$this->set('disableOtherButtons', 'yes');
		return $this;
	}
	
	public function reloadParentTable() {
		$this->set('reloadParentTable', 'yes');
		return $this;
	}
	
	/**
	 * This will trigger the page refresh on the client side
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function refresh() {
		$this->set('refresh', true);
		return $this;
	}
	
	/**
	 * This will redirect user to the given address
	 * @param  string $where
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function redirect($where) {
		$this->set('redirect', $where);
		return $this;
	}
	
	/**
	 * This will be triggered only if referrer information exists. The redirect
	 * is made on client side.
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function redirectBack() {
		$this->set('back', 'yes');
		return $this;
	}
	
	/**
	 * Update the buttons text
	 * @param string $text
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function text($text) {
		$this->set('text', $text);
		return $this;
	}
	
	/**
	 * Update the button's prompt text
	 * @param string $promptText
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function promptText($promptText) {
		$this->set('promptText', $promptText);
		return $this;
	}
	
	/**
	 * Set the array of parameters that will be sent with button's next click
	 * @param array $params
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function params(array $params) {
		$this->params = $params;
		return $this;
	}
	
	/**
	 * Set the additional param on button's next click
	 * @param string $key
	 * @param mixed $value
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function param($key, $value) {
		$this->params[$key] = $value;
		return $this;
	}
	
	/**
	 * Update the button's color
	 * @param string $color
	 * @return \Bootstrap\Response\ButtonRemote
	 */
	public function color($color) {
		if (isset(HtmlElement::$colors[$color])) {
			$this->set('color', 'btn-' . HtmlElement::$colors[$color]);
		}
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Json::flush()
	 */
	public function flush() {
		if (sizeof($this->params) > 0) {
			$this->set('params', $this->params);
		}
		
		parent::flush();
	}
}