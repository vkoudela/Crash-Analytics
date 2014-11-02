<?php namespace Bootstrap\Input;

class Image extends File {
	
	/**
	 * The array of allowed MIME types to be uploaded
	 * @var array
	 */
	protected $allowedMimes = array('image/jpeg', 'image/gif', 'image/png');
	
}