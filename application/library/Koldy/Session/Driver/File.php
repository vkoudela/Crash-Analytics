<?php namespace Koldy\Session\Driver;

use Koldy\Application;
use Koldy\Directory;
use Koldy\Exception;
use Koldy\Log;

/**
 * This is session handler that will store session files to the local
 * storage folder. You MUSTN'T use it! This class will use PHP internally
 * by it self. You just configure it all and watch the magic.
 * 
 * This requires PHP 5.4+
 * @link http://koldy.net/docs/session/file
 */
class File implements \SessionHandlerInterface {


	/**
	 * The directory where files will be stored
	 * 
	 * @var string
	 */
	protected $savePath = null;


	/**
	 * The 'options' part from config/session.php
	 * 
	 * @var array
	 */
	protected $config = array();


	/**
	 * Construct the File Session storage handler
	 * 
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		$this->config = $config;
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::open()
	 */
	public function open($save_path, $sessionid) {
		// we'll ignore $save_path because we have our own path from config

		if (isset($this->config['session_save_path'])) {
			$this->savePath = $this->config['session_save_path'];
		} else {
			$this->savePath = Application::getStoragePath() . 'session';
		}
		
		if (!is_dir($this->savePath)) {
			if (!Directory::mkdir($this->savePath, 0777)) {
				throw new Exception('Can not create directory for session storage');
			}
		}

		if (substr($this->savePath, -1) != DS) {
			$this->savePath .= DS;
		}

		return true;
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::close()
	 */
	public function close() {
		return true;
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::read()
	 */
	public function read($sessionid) {
		return (string) @file_get_contents("{$this->savePath}{$sessionid}.sess");
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::write()
	 */
	public function write($sessionid, $sessiondata) {
		return !(file_put_contents("{$this->savePath}{$sessionid}.sess", $sessiondata) === false);
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::destroy()
	 */
	public function destroy($sessionid) {
		$file = "{$this->savePath}{$sessionid}.sess";
		if (file_exists($file)) {
			unlink($file);
		}

		return true;
	}


	/**
	 * (non-PHPdoc)
	 * @see SessionHandlerInterface::gc()
	 */
	public function gc($maxlifetime) {
		foreach (glob("{$this->savePath}*") as $file) {
			if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
				unlink($file);
			}
		}

		return true;
	}

}
