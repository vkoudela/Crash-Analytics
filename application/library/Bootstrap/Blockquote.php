<?php namespace Bootstrap;
/**
 * @link  http://getbootstrap.com/css/#type-blockquotes
 */
class Blockquote extends HtmlElement {

	protected $texts = array();

	public function __construct($text = null) {
		if ($text !== null) {
			$this->p($text);
		}
	}

	public function p($text, $color = null) {
		$this->texts[] = array(
			'tag' => 'p',
			'text' => $text,
			'color' => $color
		);
		return $this;
	}

	public function small($text) {
		$this->texts[] = array(
			'tag' => 'small',
			'text' => $text
		);
		return $this;
	}

	public function getHtml() {
		$html = "<blockquote{$this->idAttr()}{$this->classAttr()}{$this->getAttributes()}>\n";
		foreach ($this->texts as $text) {
			if ($text['tag'] == 'p' && $text['color'] !== null && isset($this->colors[$text['color']])) {
				$color = " class=\"text-{$this->colors[$text['color']]}\"";
			} else {
				$color = '';
			}
			$html .= "\t<{$text['tag']}{$color}>{$text['text']}</{$text['tag']}>\n";
		}
		$html .= "</blockquote>";

		return $html;
	}
}