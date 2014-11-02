<?php namespace Koldy\Cache\Driver;

use Koldy\Application;
use Koldy\Directory;
use Koldy\Exception;
use Koldy\Log;

class Files extends AbstractCacheDriver {


	/**
	 * The path to the folder where cache files will be stored
	 * 
	 * @var string
	 */
	protected $path = null;


	/**
	 * The array of loaded and/or data that will be stored
	 * 
	 * @var array
	 */
	private $data = array();


	/**
	 * Flag if shutdown function is registered or not
	 * 
	 * @var boolean
	 */
	private $shutdown = false;


	/**
	 * Construct the object by array of config properties. Config keys are set
	 * in config/cache.php and this array will contain only block for the
	 * requested cache driver. Yes, you can also build this manually, but that
	 * is not recommended.
	 * 
	 * @param array $config
	 * @throws \Koldy\Exception
	 */
	public function __construct(array $config) {
		// because if cache is not enabled, then lets not do anything else

		if (!isset($config['path']) || $config['path'] === null) {
			$this->path = Application::getStoragePath('cache/');
		} else {
			$this->path = $config['path'];
		}
		
		if (substr($this->path, -1) != '/') {
			$this->path .= '/';
		}

		if (!is_dir($this->path) && !Directory::mkdir($this->path, 0777)) {
			throw new Exception("Cache directory '{$this->path}' doesn't exists and can't be created");
		}

		parent::__construct($config);
	}


	/**
	 * Get path to the cache file by $key
	 * 
	 * @param string $key
	 * @return string
	 */
	protected function getPath($key) {
		return $this->path . $key . '_' . md5($key . Application::getConfig('application', 'key'));
	}


	/**
	 * Load the data from the file and store it in this request's memory
	 * 
	 * @param string $key
	 * @return stdClass or false if cache doesn't exists
	 */
	protected function load($key) {
		$this->checkKey($key);
		$path = $this->getPath($key);

		if (is_file($path)) {
			$object = new \stdClass;
			$object->path = $path;

			$file = file_get_contents($path);
			$firstLine = substr($file, 0, strpos($file, "\n"));
			$semicolon = strpos($firstLine, ';');
			$firstLine = explode(';', $firstLine);

			$object->created = strtotime($firstLine[0]);
			$object->seconds = $firstLine[1];
			$object->data = substr($file, strpos($file, "\n") +1);
			$object->action = null;
			$object->type = $firstLine[2];

			switch($object->type) {
				case 'array':
				case 'object':
					$object->data = unserialize($object->data);
					break;
			}

			$this->data[$key] = $object;
			return $object;
		}

		return false;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::get()
	 */
	public function get($key) {
		$this->checkKey($key);

		if ($this->has($key)) {
			return $this->data[$key]->data;
		}

		return null;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::set()
	 */
	public function set($key, $value, $seconds = null) {
		$this->checkKey($key);

		if ($seconds === null) {
			$seconds = $this->defaultDuration;
		}

		if (isset($this->data[$key])) {
			$object = $this->data[$key];
		} else {
			$object = new \stdClass;
			$object->path = $this->getPath($key);
		}

		$object->created = time();
		$object->seconds = $seconds;
		$object->data = $value;
		$object->action = 'set';
		$object->type = gettype($value);
		$this->data[$key] = $object;

		$this->initShutdown();
		return true;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::add()
	 */
	public function add($key, $value, $seconds = null) {
		$this->checkKey($key);

		if ($this->has($key)) {
			return false;
		}

		return $this->set($key, $value, $seconds);
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::has()
	 */
	public function has($key) {
		$this->checkKey($key);

		if (!isset($this->data[$key])) {
			$object = $this->load($key);
			if ($object === false) {
				return false;
			}
		} else {
			$object = $this->data[$key];
		}

		$ok = $object->created + $object->seconds > time();
		if (!$ok) {
			$this->data[$key]->action = 'delete';
			$this->initShutdown();
		}

		return $ok;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractCacheDriver::delete()
	 */
	public function delete($key) {
		$this->checkKey($key);

		if (!is_file($this->getPath($key))) {
			return null;
		}

		if (isset($this->data[$key])) {
			$this->data[$key]->action = 'delete';
			$this->initShutdown();
			// cache files will be deleted after request ends
			return true;
		} else {
			return @unlink($this->getPath($key));
		}
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractDriver::deleteAll()
	 */
	public function deleteAll() {
		Directory::emptyDirectory($this->path);
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Cache\Driver\AbstractDriver::deleteOld()
	 */
	public function deleteOld($olderThenSeconds = null) {
		if ($olderThenSeconds === null) {
			$olderThenSeconds = $this->defaultDuration;
		}

		clearstatcache();

		/**
		 * This is probably not good since lifetime is written in file
		 * But going into every file and read might be even worse idea
		 * XXX: Think about this
		 */
		foreach (Directory::read($this->path) as $fullPath => $fileName) {
			$timeCreated = @filemtime($fullPath);
			if ($timeCreated !== false) {
				// successfully red the file modification time

				if (time() - $olderThenSeconds > $timeCreated) {
					// it is old enough to be removed

					if (!@unlink($fullPath)) {
						Log::warning("Can not delete cached file on path {$fullPath}");
					}
				}
			}
		}
	}


	/**
	 * Initialize the shutdown function when request execution ends
	 */
	protected function initShutdown() {
		if (!$this->shutdown) {
			$this->shutdown = true;
			$self = $this;
			register_shutdown_function(function() use ($self) {
				$self->shutdown();
			});
		}
	}


	/**
	 * Execute this method on request's execution end. When you're working with
	 * cache, the idea is not to work all the time with the filesystem. All
	 * changes (new keys and keys that exists and needs to be deleted) will be
	 * applied here, on request shutdown
	 */
	public function shutdown() {
		foreach ($this->data as $key => $object) {
			switch ($object->action) {
				case 'set':
					switch($object->type) {
						default:
							$data = $object->data;
							break;

						case 'array':
						case 'object':
							$data = serialize($object->data);
							break;
					}

					@file_put_contents(
						$object->path,
						sprintf("%s;%d;%s\n%s",
							date('r', $object->created),
							$object->seconds,
							$object->type,
							$data
						)
					);
					break;

				case 'delete':
					@unlink($object->path);
					break;
			}
		}
	}

}
