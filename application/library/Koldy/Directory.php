<?php namespace Koldy;

/**
 * Class for manipulation with directories on server
 *
 */
class Directory {


	/**
	 * Get the list of all files and folders from the given folder
	 * 
	 * @param string $path the directory path to read
	 * @param string $filter [optional] regex for filtering the list
	 * @return array assoc; the key is full path of the file and value is only file name
	 * @example return array('/Users/vkoudela/Sites/site.tld/folder/croatia.png' => 'croatia.png')
	 */
	public static function read($path, $filter = null) {
		if (is_dir($path) && $handle = opendir($path)) {
			$files = array();
			if (substr($path, -1) != '/') {
				$path .= '/';
			}
			while (false !== ($entry = readdir($handle))) {
				if ($entry !== '.' && $entry !== '..') {
					if ($filter === null || preg_match($filter, $entry)) {
						$files[$path . $entry] = $entry;
					}
				}
			}
			return $files;
		} else {
			return null;
		}
	}


	/**
	 * Get the list of all only files from the given folder
	 * 
	 * @param string $path the directory path to read
	 * @param string $filter [optional] regex for filtering the list
	 * @return array assoc; the key is full path of the file and value is only file name
	 * @example return array('/Users/vkoudela/Sites/site.tld/folder/croatia.png' => 'croatia.png')
	 */
	public static function readFiles($path, $filter = null) {
		if (is_dir($path) && $handle = opendir($path)) {
			$files = array();
			if (substr($path, -1) != '/') {
				$path .= '/';
			}
			while (false !== ($entry = readdir($handle))) {
				if ($entry !== '.' && $entry !== '..' && !is_dir($path . $entry)) {
					if ($filter === null || preg_match($filter, $entry)) {
						$files[$path . $entry] = $entry;
					}
				}
			}
			return $files;
		} else {
			return null;
		}
	}


	/**
	 * Create the target directory
	 * 
	 * @param string $path
	 * @param octal $chmod default 0644
	 * @return bool was it successfull
	 * @example $chmod 0777, 0755, 0700
	 */
	public static function mkdir($path, $chmod = 0644) {
		if (is_dir($path)) {
			return true;
		}

		$paths = explode(DS, $path);
		if (sizeof($paths)) {
			array_shift($paths);
			$path = DS;

			foreach($paths as $key => $dir) {
				$path .= $dir . DS;
				if (!is_dir($path)) {
					if (!@mkdir($path, $chmod)) {
						return false;
					}
				}
			}

			return true;
		}
		return false;
	}


	/**
	 * Remove directory and content inside recursively
	 * 
	 * @param string $directory
	 * @return boolean
	 */
	public static function rmdirRecursive($directory) {
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
			$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
		}
		rmdir($directory);
		return true;
	}


	/**
	 * Empty all directory content, but do not delete the directory
	 * 
	 * @param string $directory
	 * @return boolean
	 */
	public static function emptyDirectory($directory) {
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
			$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
		}
		return true;
	}

}
