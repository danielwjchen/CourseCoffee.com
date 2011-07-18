<?php
/**
 * @file
 * API for handling file uploads and server file management.
 *
 * This is inspired by Drupal
 */


class File {

	/**
	 * Flag used by file_prepare_directory() -- create directory if not present.
	 */
	const CREATE_DIRECTORY = 1;

	/**
	 * Flag used by file_prepare_directory() -- file permissions may be changed.
	 */
	const MODIFY_PERMISSIONS = 2;

	/**
	 * Flag for dealing with existing files: Appends number until name is unique.
	 */
	const EXISTS_RENAME = 0;

	/**
	 * Flag for dealing with existing files: Replace the existing file.
	 */
	const EXISTS_REPLACE = 1;

	/**
	 * Flag for dealing with existing files: Do nothing and return FALSE.
	 */
	const EXISTS_ERROR = 2;

	/**
	 * Indicates that the file is permanent and should not be deleted.
	 *
	 * Temporary files older than DRUPAL_MAXIMUM_TEMP_FILE_AGE will be removed
	 * during cron runs, but permanent files will not be removed during the file
	 * garbage collection process.
	 */
	const STATUS_PERMANENT = 1;

	/**
	 * Check that the directory exists and is writable.
	 *
	 * Directories need to have execute permissions to be considered a directory by
	 * FTP servers, etc.
	 *
	 * @param &$directory
	 *   A string reference containing the name of a directory path or URI. A
	 *   trailing slash will be trimmed from a path.
	 * @param $options
	 *   A bitmask to indicate if the directory should be created if it does
	 *   not exist (FILE_CREATE_DIRECTORY) or made writable if it is read-only
	 *   (FILE_MODIFY_PERMISSIONS).
	 *
	 * @return
	 *   TRUE if the directory exists (or was created) and is writable. FALSE
	 *   otherwise.
	 */
	public static function PrepareDirectory(&$directory, $options = self::MODIFY_PERMISSIONS) {
		if (!file_stream_wrapper_valid_scheme(file_uri_scheme($directory))) {
			// Only trim if we're not dealing with a stream.
			$directory = rtrim($directory, '/\\');
		}

		// Check if directory exists.
		if (!is_dir($directory)) {
			// Let mkdir() recursively create directories and use the default directory
			// permissions.
			if (($options & self::CREATE_DIRECTORY) && @drupal_mkdir($directory, NULL, TRUE)) {
				return drupal_chmod($directory);
			}
			return FALSE;
		}
		// The directory exists, so check to see if it is writable.
		$writable = is_writable($directory);
		if (!$writable && ($options & self::MODIFY_PERMISSIONS)) {
			return drupal_chmod($directory);
		}

		return $writable;
	}

	/**
	 * Finds all files that match a given mask in a given directory.
	 *
	 * Directories and files beginning with a period are excluded; this prevents 
	 * hidden files and directories (such as SVN working directories) from being 
	 * scanned.
	 *
	 * @param string $dir 
	 *  The base directory or URI to scan, without trailing slash.
	 * @oaram string $mask
	 *  The preg_match() regular expression of the files to find.
	 * @param array options
	 *  An associative array of additional options, with the following elements:
	 *   - 'nomask': The preg_match() regular expression of the files to ignore. 
	 *     Defaults to '/(\.\.?|CVS)$/'
	 *   - 'callback': The callback function to call for each match. There is no 
	 *     default callback.
	 *   - recurse': When TRUE, the directory scan will recurse the entire tree 
	 *     starting at the provided directory. Defaults to TRUE.
	 *   - 'key': The key to be used for the returned associative array of files. 
	 *     Possible values are 'uri', for the file's URI; 'filename', for the 
	 *     basename of the file; and 'name' for the name of the file without the 
	 *     extension. Defaults to 'uri'.
	 *   - 'min_depth': Minimum depth of directories to return files from. 
	 *     Defaults to 0.
	 * @param int $depth
	 *  Current depth of recursion. This parameter is only used internally and 
	 *  should not be passed in
	 *
	 * @return
	 *  An associative array (keyed on the chosen key) of objects with 'uri', 
	 *  'filename', and 'name' members corresponding to the matching files.
	 */
	public static function ScanDirectory($dir, $mask, $options = array(), $depth = 0) {
		// Merge in defaults.
		$options += array(
			'nomask' => '/(\.\.?|CVS)$/',
			'callback' => 0,
			'recurse' => TRUE,
			'key' => 'uri',
			'min_depth' => 0,
		);

		$options['key'] = in_array($options['key'], array('uri', 'filename', 'name')) ? $options['key'] : 'uri';
		$files = array();
		if (is_dir($dir) && $handle = opendir($dir)) {
			while (FALSE !== ($filename = readdir($handle))) {
				if (!preg_match($options['nomask'], $filename) && $filename[0] != '.') {
					$uri = "$dir/$filename";
					if (is_dir($uri) && $options['recurse']) {
						// Give priority to files in this folder by merging them in after any subdirectory files.
						$files = array_merge(self::ScanDirectory($uri, $mask, $options, $depth + 1), $files);
					}
					elseif ($depth >= $options['min_depth'] && preg_match($mask, $filename)) {
						// Always use this match over anything already set in $files with the
						// same $$options['key'].
						$file = new stdClass();
						$file->uri = $uri;
						$file->filename = $filename;
						$file->name = pathinfo($filename, PATHINFO_FILENAME);
						$key = $options['key'];
						$files[$file->$key] = $file;
						if ($options['callback']) {
							$options['callback']($uri);
						}
					}
				}
			}

			closedir($handle);
		}

		return $files;
	}

}
