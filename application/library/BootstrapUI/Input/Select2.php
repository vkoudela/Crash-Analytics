<?php namespace BootstrapUI\Input;
/**
 * The Select2 Bootstrap component.
 * @author vkoudela
 * @link http://ivaynberg.github.io/select2/
 */
class Select2 extends \Bootstrap\Input\Select {
	
	/**
	 * The select2 config
	 * @var array
	 */
	protected $config = array();
	
	/**
	 * Flag to render this as <select> or as <input type=hidden>
	 * @var bool
	 */
	private $renderAsSelect = true;

	/**
	 * Construct the object for select element/dropdown/combobox - call it whatever
	 * @param string $name
	 * @param string $label [optional]
	 * @param array $values [optional] assoc array
	 * @param mixed $value [optional]
	 */
	public function __construct($name, $label = null, array $values = array(), $value = null) {
		parent::__construct($name, $label, $values, $value);
		static::injectDependencies();
		$this->addClass('select2');
	}
	
	/**
	 * Inject the dependencies
	 */
	public static function injectDependencies() {
		\BootstrapUI::addDependency(array(
			'3rd/select2-3.4.3/select2.css',
			'3rd/select2-bootstrap3.css',
			'3rd/select2-3.4.3/select2.min.js',
			'3rd/select2-bind.js'
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\Input\Select::getInputHtml()
	 */
	protected function getInputHtml() {
		if ($this->renderAsSelect) {
			return parent::getInputHtml();
		} else {
			$this->setAttribute('style', 'width: 100%');
			return "<input type=\"hidden\"{$this->classAttr()}{$this->idAttr()}{$this->getAttributes()} />";
		}
	}
	
	/**
	 * Enable tagging support
	 * @param array $tags the array of initial tags
	 * @return \BootstrapUI\Input\Select2
	 */
	public function tags(array $tags = null) {
		$this->config['tags'] = ($tags !== null) ? array_values($tags) : array();
		$this->renderAsSelect = false;
		return $this;
	}

	/**
	 * Set the element's value
	 * @param string $value
	 * @return \BootstrapUI\Input\Select2
	 */
	public function value($value) {
		$this->value = $value;
		return $this->setAttribute('value', $value);
	}
	
	/**
	 * Set token separator for tags
	 * @param string|array $separator
	 * @return \BootstrapUI\Input\Select2
	 */
	public function tagsTokenSeparator($separator) {
		$separator = !is_array($separator) ? array($separator) : array_values($separator);
		$this->config['tokenSeparators'] = $separator;
		$this->renderAsSelect = false;
		return $this;
	}
	
	/**
	 * Set the max input length
	 * @param int $maxLength
	 * @return \BootstrapUI\Input\Select2
	 */
	public function maxLength($maxLength) {
		$this->config['maximumInputLength'] = (int) $maxLength;
		return $this;
	}
	
	/**
	 * The maximum number of selections
	 * @param int $howMuch
	 * @return \BootstrapUI\Input\Select2
	 */
	public function maxSelections($howMuch) {
		$this->config['maximumSelectionSize'] = (int) $howMuch;
		return $this;
	}
	
	/**
	 * Set the placeholder text
	 * @param string $text
	 * @return \BootstrapUI\Input\Select2
	 */
	public function placeholder($text) {
		$this->config['placeholder'] = $text;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\Input\Select::getHtml()
	 */
	public function getHtml() {
		$this->data('config', base64_encode(json_encode($this->config)));
		return parent::getHtml();
	}
}