<?php namespace Koldy;

/**
 * The view class will properly serve prepared HTML to user.
 * 
 * This framework doesn't have and doesn't use any templating engine so there is
 * no need to learn extra syntax or what so ever. All you need to know is how to
 * set up file structure.
 * 
 * View files should contain all of your HTML code and should never do any logic
 * or data fetching. Try to keep your code clean and in MVC style.
 * 
 * All view files are located in /application/views folder and must have
 * .phtml extension.
 * 
 * @link http://koldy.net/docs/view
 */
class View extends Response {


	/**
	 * View file that will be rendered
	 * 
	 * @var string
	 */
	private $view = null;


	/**
	 * Create the object with base view
	 * 
	 * @param string $view
	 * @return \Koldy\View
	 * @example View::create('base') will initialize /application/views/base.phtml
	 * @link http://koldy.net/docs/view
	 */
	public static function create($view) {
		$self = new static();
		return $self->setView($view);
	}


	/**
	 * Set view after object initialization
	 * 
	 * @param string $view
	 * @return \Koldy\View
	 */
	public function setView($view) {
		$this->view = $view;
		return $this;
	}


	/**
	 * Add key value pair of data that will be accesible in the view files
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @throws \Exception
	 * @return \Koldy\View
	 * @link http://koldy.net/docs/view#passing-variables
	 */
	public function with($key, $value) {
		if ($key == 'view') {
			throw new Exception('You can not use key name that exists as reserved property in View class');
		}
		$this->$key = $value;
		return $this;
	}


	/**
	 * Is the given key set or not
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return property_exists($this, $key);
	}


	/**
	 * Add the array of values that will be accessible in the view
	 * 
	 * @param array $with
	 * @return \Koldy\View
	 * @link http://koldy.net/docs/view#passing-variables
	 */
	public function params(array $with) {
		foreach ($with as $key => $value) {
			if ($key == 'view') {
				throw new Exception('You can not use key name that exists as reserved property in View class');
			}
			$this->$key = $value;
		}

		return $this;
	}


	/**
	 * Get the path of the view
	 * 
	 * @param string $view
	 * @return string
	 */
	protected static function getViewPath($view) {
		if (DS != '/') {
			$view = str_replace('/', DS, $view);
		}

		$pos = strpos($view, ':');
		if ($pos === false) {
			return Application::getApplicationPath() . 'views' . DS . str_replace('.', DS, $view) . '.phtml';
		} else {
			return Application::getApplicationPath() . 'modules'
				. DS . substr($view, 0, $pos)
				. DS . 'views'
				. DS . str_replace('.', DS, substr($view, $pos +1)) . '.phtml';
		}
	}


	/**
	 * Does view exists or not
	 * 
	 * @param string $view
	 * @return boolean
	 */
	public static function exists($view) {
		$path = static::getViewPath($view);
		return is_file($path);
	}


	/**
	 * Render some other view file inside of parent view file
	 * 
	 * @param string $view
	 * @return string
	 */
	public function render($view) {
		$path = static::getViewPath($view);

		if (!file_exists($path)) {
			Log::error("Can not find view on path={$path}");
			throw new Exception("View ({$view}) not found");
		}

		ob_start();
		include($path);
		return ob_get_clean();
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Response::flush()
	 */
	public function flush() {
		$this->header('Connection', 'close');
		$path = static::getViewPath($this->view);

		if (!file_exists($path)) {
			Log::error("Can not find view on path={$path}");
			throw new Exception("View ({$this->view}) not found");
		}

		ob_start();

			include($path);
			$size = ob_get_length();
			$this->header('Content-Length', $size);

		$this->flushHeaders();
		ob_end_flush();
		flush();
		
		if (function_exists('fastcgi_finish_request')) {
			@fastcgi_finish_request();
		}

		if ($this->workAfterResponse !== null) {
			call_user_func($this->workAfterResponse);
		}
	}


	/**
	 * Get the rendered view code.
	 * If you set after() function, rembember that this method won't
	 * do anything with that work.
	 * 
	 * @return string
	 * @link http://koldy.net/docs/view#get-output
	 */
	public function getOutput() {
		$path = static::getViewPath($this->view);

		if (!file_exists($path)) {
			Log::error("Can not find view on path={$path}");
			throw new Exception("View ({$this->view}) not found");
		}

		ob_start();
		include($path);
		return ob_get_clean();
	}

}
