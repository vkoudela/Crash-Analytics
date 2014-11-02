<?php namespace BootstrapUI\Button;
/**
 * This button will make ajax request on click. You can set parameters, prompt and progress text.
 * In response, you can manage how to transform button.
 * @author vkoudela
 *
 */
class Remote extends \Bootstrap\Button {
	
	/**
	 * The parameters the will be sent to server
	 * @var array
	 */
	private $params = array();
	
	/**
	 * @param string $text
	 */
	public function __construct($text) {
		parent::__construct($text);
		$this->addClass('x-button-remote');
		$this->setAttribute('onclick', 'BootstrapUI.Button.Remote.click(this)');
	}
	
	/**
	 * @param string $url
	 * @return \BootstrapUI\Button\Remote
	 */
	public function url($url) {
		return $this->data('url', $url);
	}
	
	/**
	 * @param string $progressText
	 * @return \BootstrapUI\Button\Remote
	 */
	public function progressText($progressText) {
		$this->data('original-text', $this->text);
		return $this->data('progress-text', $progressText);
	}
	
	/**
	 * @param string $promptText
	 * @return \BootstrapUI\Button\Remote
	 */
	public function promptText($promptText) {
		return $this->data('prompt-text', $promptText);
	}
	
	/**
	 * Add extra param that will be sent to server as post parameter
	 * @param string $key
	 * @param mixed $value
	 * @return \BootstrapUI\Button\Remote
	 */
	public function param($key, $value) {
		$this->params[$key] = $value;
		return $this;
	}
	
	/**
	 * Add the array of extra params
	 * @param array $params
	 * @return \BootstrapUI\Button\Remote
	 */
	public function params(array $params) {
		foreach ($params as $key => $value) {
			$this->params[$key] = $value;
		}
		
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\Button::getHtml()
	 */
	public function getHtml() {
		if (sizeof($this->params) > 0) {
			$this->data('params', base64_encode(\Koldy\Json::encode($this->params)));
		}
		
		return parent::getHtml();
	}
}