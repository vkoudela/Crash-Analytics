<?php namespace Bootstrap\Input;

class Numberfield extends Textfield {

	/**
	 * The element's type, by default 'text'
	 * @var string
	 */
	protected $type = 'number';
	
	/**
	 * Minimum value of field
	 * @param int $minimumValue
	 * @return \Bootstrap\Input\Numberfield
	 */
	public function min($minimumValue) {
		return $this->setAttribute('min', $minimumValue);
	}
	
	/**
	 * Maxium value of field
	 * @param int $maximumValue
	 * @return \Bootstrap\Input\Numberfield
	 */
	public function max($maximumValue) {
		return $this->setAttribute('max', $maximumValue);
	}
	
	/**
	 * The step interval
	 * @param int $stepInterval
	 * @return \Bootstrap\Input\Numberfield
	 */
	public function step($stepInterval) {
		return $this->setAttribute('step', $stepInterval);
	}

}
