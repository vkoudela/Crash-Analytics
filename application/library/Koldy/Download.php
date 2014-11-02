<?php namespace Koldy;

/**
 * Force file download to user.
 *
 */
class Download extends Response {


	/**
	 * This variable will not be null if you want to download some content
	 * (text, binary or anything else)
	 * 
	 * @var string or binary
	 */
	protected $content = null;


	/**
	 * This variable will contain path to file you want to download
	 * 
	 * @var string
	 */
	protected $file = null;


	/**
	 * Download as name (file name that user will get)
	 * 
	 * @var string
	 */
	protected $asName = null;


	/**
	 * The content type of download
	 * 
	 * @var string
	 */
	protected $contentType = null;


	/**
	 * The file size - it will be automatically calculated, but if you
	 * don't want to calculate it, then set the size manually here
	 * 
	 * @var int in bytes
	 */
	protected $fileSize = null;


	/**
	 * Return download of dynamic content
	 * 
	 * @param mixed $content
	 * @param string $asName
	 * @param string $contentType
	 * @throws Exception
	 * @return \Koldy\Download
	 */
	public static function content($content, $asName, $contentType) {
		$self = new static();

		if ($self->file !== null) {
			throw new Exception('Can not output content when file download is set');
		}

		$self->content = $content;
		$self->asName = $asName;
		$self->contentType = $contentType;
		return $self;
	}


	/**
	 * Return file download
	 * 
	 * @param string $path
	 * @param string $asName [optional]
	 * @param string $contentType [optional]
	 * @throws Exception
	 * @return \Koldy\Download
	 */
	public static function file($path, $asName = null, $contentType = null) {
		$self = new static();

		if ($self->content !== null) {
			throw new Exception('Can not download file when content download is set');
		}

		$self->file = $path;
		$self->asName = $asName;
		$self->contentType = $contentType;
		return $self;
	}


	/**
	 * Set the file size manually
	 * 
	 * @param int $fileSize in bytes
	 * @return \Koldy\Download
	 * @example return Download::file('file.zip')->fileSize(123000);
	 */
	public function fileSize($fileSize) {
		$this->fileSize = $fileSize;
		return $this;
	}


	/**
	 * (non-PHPdoc)
	 * @see \Koldy\Response::flush()
	 */
	public function flush() {
		$this
			->header('Connection', 'close')
			->header('Pragma', 'public')
			->header('Expires', 0)
			->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
			->header('Cache-Control', 'public')
			->header('Content-Description', 'File Transfer');

		if ($this->content !== null) {

			// it is dynamic content download!
			$this->header('Content-Type', $this->contentType)
				->header('Content-Length', strlen($this->content))
				->header('Content-Disposition', "attachment; filename=\"{$this->asName}\";")
				->header('Content-Transfer-Encoding', 'binary');

			$this->flushHeaders();
			print $this->content;
			flush();

		} else {

			// it is file download!
			if (!is_file($this->file)) {
				throw new Exception('Can not download file: ' . $this->file);
			}

			if ($this->fileSize !== null) {
				$this->header('Content-Length', $this->fileSize);
			}

			if ($this->asName === null) {
				$this->asName = basename($this->file);
			}

			if ($this->contentType === null) {
				$extension = pathinfo($this->file, PATHINFO_EXTENSION);
				$this->contentType = Http\Mime::getMimeByExtension($extension);
				if ($this->contentType === null) {
					$this->contentType = 'application/force-download'; 
				}
			}

			$this
				->header('Content-Type', $this->contentType)
				->header('Content-Disposition', "attachment; filename=\"{$this->asName}\";")
				->header('Content-Transfer-Encoding', 'binary');

			set_time_limit(0);
			$this->flushHeaders();

			$file = @fopen($this->file, 'rb');
			while(!feof($file)) {
				print(@fread($file, 8192)); // download in chunks per 8kb
				flush();
			}

			// XXX: Check this few more times
			@fclose($file);
			ob_flush();
			flush();
		}

		if (function_exists('fastcgi_finish_request')) {
			@fastcgi_finish_request();
		}

		if ($this->workAfterResponse !== null) {
			call_user_func($this->workAfterResponse);
		}
	}

}
