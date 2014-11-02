<?php namespace Bootstrap;

class Panel extends NestableElement {

	/**
	 * @var string
	 */
	protected $title = null;

	/**
	 * @var array<mixed>
	 */
	protected $headerElements = array();

	/**
	 * @var mixed
	 */
	protected $content = null;

	/**
	 * @var mixed
	 */
	protected $footer = null;

	/**
	 * @var string
	 */
	private $color = null;
	
	/**
	 * Is this panel collapsed or not? True for yes, false for no, null for not using
	 * @var bool
	 */
	private $collapsible = null;

	/**
	 * Construct the panel
	 * @param string $title [optional]
	 * @param string $content [optional]
	 * @param string $footer [optional]
	 */
	public function __construct($title = null, $content = null, $footer = null) {
		$this->title = $title;
		$this->footer = $footer;
		
		if ($content !== null) {
			$this->add($content);
		}
	}

	/**
	 * Set the panel title
	 * @param string $title
	 * @return \Bootstrap\Panel
	 */
	public function title($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 * Add the element to the top right side of panel's title
	 * @param mixed $button
	 * @return \Bootstrap\Panel
	 */
	public function addHeaderElement($element) {
		$this->headerElements[] = $element;
		return $this;
	}

	/**
	 * Set the content of panel's body. You can also pass the array of elements.
	 * If you pass only \Bootstrap\Table or \Bootstrap\Table\Remote element, then
	 * table will be rendered as <table> inside of panel, not inside of panel-body.
	 * @param mixed $content
	 * @return \Bootstrap\Panel
	 */
	public function content($content) {
		$this->removeElements()->add($content);
		return $this;
	}

	/**
	 * Set the panel's footer
	 * @param mixed $footer
	 * @return \Bootstrap\Panel
	 */
	public function footer($footer) {
		$this->footer = $footer;
		return $this;
	}

	/**
	 * Set the bootstrap color.
	 * @param string $color
	 * @return \Bootstrap\Panel
	 */
	public function color($color) {
		if (isset(static::$colors[$color])) {
			$this->addClass('panel-' . static::$colors[$color]);
			$this->color = $color;
		}
		return $this;
	}
	
	/**
	 * Use option for collapsing panel's body and footer.
	 * @param bool|null $collapsed true to init expanded, false to hide, null to not use it
	 * @return \Bootstrap\Panel
	 */
	public function collapsible($collapsed = true) {
		$this->collapsible = $collapsed;
		$this->addHeaderElement(\Bootstrap::anchor('', '')
			->addClass('x-panel-collapsible')
			->addClass('btn-xs')
			->data('up', 'collapse-up')
			->data('down', 'collapse-down')
		);
		$this->data('collapsed', $collapsed ? 'true' : 'false');
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bootstrap\HtmlElement::getHtml()
	 */
	public function getHtml() {
		if ($this->color === null) {
			$this->color('default');
		}
		
		$html = "<div{$this->classAttr('panel')}{$this->idAttr()}{$this->getAttributes()}>";

		if ($this->title !== null) {
			if (sizeof($this->headerElements) > 0) {
				$elements = '<div class="pull-right x-panel-header-elements">' . implode("\n", $this->headerElements) . '</div>';
			} else {
				$elements = '';
			}
			$html .= "<div class=\"panel-heading\">{$elements}<h3 class=\"panel-title x-panel-title\">{$this->title}</h3></div>";
		}
		
		if ($this->content instanceof \Bootstrap\Table) {
			$html .= $this->getElementsHtml();
		} else {
			$style = ($this->collapsible === false ? ' style="display:none;"' : '');
			$html .= '<div class="panel-body"' . $style . '>';
			$html .= $this->getElementsHtml();
			$html .= '<div class="clearfix"></div></div>';
		}

		if ($this->footer !== null) {
			$html .= "<div class=\"panel-footer\">{$this->footer}</div>";
		}

		$html .= '</div>';

		return $html;
	}
}
