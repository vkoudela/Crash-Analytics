<?php namespace BootstrapUI\Input;

use Bootstrap\Icon;
use BootstrapUI\Button\Remote as RemoteButton;

// TODO: Izvaditi ovo van, da ne bude tako vezano za input=file nego da bude univerzalno

class FilePreview extends \Bootstrap\HtmlElement {
	
	/**
	 * Visible icon that identifies this file
	 * @var string or object
	 */
	protected $icon = null;
	
	/**
	 * Is this file deletable or not
	 * @var RemoteButton
	 */
	protected $deletable = null;
	
	/**
	 * Set the Bootstrap icon as preview icon
	 * @param Icon $icon
	 * @return \BootstrapUI\Input\FilePreview
	 */
	public function icon(Icon $icon) {
		$this->icon = $icon;
		return $this;
	}
	
	/**
	 * Set image as preview
	 * @param string $src
	 * @return \BootstrapUI\Input\FilePreview
	 */
	public function image($src) {
		$this->icon = $src;
		return $this;
	}
	
	/**
	 * Make this preview deletable
	 * @param RemoteButton $button
	 * @return \BootstrapUI\Input\FilePreview
	 */
	public function deletable(RemoteButton $button) {
		$this->deletable = $button;
		return $this;
	}
	
	public function getHtml() {
		$html = '';
		
		return $html;
	}
	
}