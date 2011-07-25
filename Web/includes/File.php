<?php
/**
 * @file
 * API for handling file uploads and server file management.
 *
 * This is inspired by Drupal
 */


class File {

	/**
	 * @defgroup file_constants
	 * @{
	 * Define a set of constants to be used as flags
	 */
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
	 * @} End of group "file_constants"
	 */

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

	/**
	 * Saves a file upload to a new location. The source file is validated as a
	 * proper upload and handled as such.
	 *
	 * The file will be added to the files table as a temporary file. Temporary files
	 * are periodically cleaned. To make the file permanent file call
	 * file_set_status() to change its status.
	 *
	 * @param $source
	 *   A string specifying the name of the upload field to save.
	 * @param $validators
	 *   An optional, associative array of callback functions used to validate the
	 *   file. The keys are function names and the values arrays of callback
	 *   parameters which will be passed in after the file object. The
	 *   functions should return an array of error messages; an empty array
	 *   indicates that the file passed validation. The functions will be called in
	 *   the order specified.
	 * @param $dest
	 *   A string containing the directory $source should be copied to. If this is
	 *   not provided or is not writable, the temporary directory will be used.
	 * @param $replace
	 *   A boolean indicating whether an existing file of the same name in the
	 *   destination directory should overwritten. A false value will generate a
	 *   new, unique filename in the destination directory.
	 * @return
	 *   An object containing the file information, or 0 in the event of an error.
	 */
	function saveUpload($source, $validators = array(), $dest = FALSE, $replace = FILE_EXISTS_RENAME) {
		static $upload_cache;

		// Add in our check of the the file name length.
		$validators['file_validate_name_length'] = array();

		// Return cached objects without processing since the file will have
		// already been processed and the paths in _FILES will be invalid.
		if (isset($upload_cache[$source])) {
			return $upload_cache[$source];
		}

		// If a file was uploaded, process it.
		if (isset($_FILES['files']) && $_FILES['files']['name'][$source] && is_uploaded_file($_FILES['files']['tmp_name'][$source])) {
			// Check for file upload errors and return FALSE if a
			// lower level system error occurred.
			switch ($_FILES['files']['error'][$source]) {
				// @see http://php.net/manual/en/features.file-upload.errors.php
				case UPLOAD_ERR_OK:
					break;

				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					drupal_set_message(t('The file %file could not be saved, because it exceeds %maxsize, the maximum allowed size for uploads.', array('%file' => $source, '%maxsize' => format_size(file_upload_max_size()))), 'error');
					return 0;

				case UPLOAD_ERR_PARTIAL:
				case UPLOAD_ERR_NO_FILE:
					drupal_set_message(t('The file %file could not be saved, because the upload did not complete.', array('%file' => $source)), 'error');
					return 0;

					// Unknown error
				default:
					drupal_set_message(t('The file %file could not be saved. An unknown error has occurred.', array('%file' => $source)), 'error');
					return 0;
			}

			// Build the list of non-munged extensions.
			// @todo: this should not be here. we need to figure out the right place.
			$extensions = '';
			foreach ($user->roles as $rid => $name) {
				$extensions .= ' '. variable_get("upload_extensions_$rid",
				variable_get('upload_extensions_default', 'jpg jpeg gif png txt html doc xls pdf ppt pps odt ods odp'));
			}

			// Begin building file object.
			$file = new stdClass();
			$file->filename = file_munge_filename(trim(basename($_FILES['files']['name'][$source]), '.'), $extensions);
			$file->filepath = $_FILES['files']['tmp_name'][$source];
			$file->filemime = file_get_mimetype($file->filename);

			// If the destination is not provided, or is not writable, then use the
			// temporary directory.
			if (empty($dest) || file_check_path($dest) === FALSE) {
				$dest = file_directory_temp();
			}

			$file->source = $source;
			$file->destination = file_destination(file_create_path($dest .'/'. $file->filename), $replace);
			$file->filesize = $_FILES['files']['size'][$source];

			// Call the validation functions.
			$errors = array();
			foreach ($validators as $function => $args) {
				array_unshift($args, $file);
				// Make sure $file is passed around by reference.
				$args[0] = &$file;
				$errors = array_merge($errors, call_user_func_array($function, $args));
			}

			// Rename potentially executable files, to help prevent exploits.
			if (preg_match('/\.(php|pl|py|cgi|asp|js)$/i', $file->filename) && (substr($file->filename, -4) != '.txt')) {
				$file->filemime = 'text/plain';
				$file->filepath .= '.txt';
				$file->filename .= '.txt';
				// As the file may be named example.php.txt, we need to munge again to
				// convert to example.php_.txt, then create the correct destination.
				$file->filename = file_munge_filename($file->filename, $extensions);
				$file->destination = file_destination(file_create_path($dest .'/'. $file->filename), $replace);
			}


			// Check for validation errors.
			if (!empty($errors)) {
				$message = t('The selected file %name could not be uploaded.', array('%name' => $file->filename));
				if (count($errors) > 1) {
					$message .= '<ul><li>'. implode('</li><li>', $errors) .'</li></ul>';
				}
				else {
					$message .= ' '. array_pop($errors);
				}
				form_set_error($source, $message);
				return 0;
			}

			// Move uploaded files from PHP's upload_tmp_dir to Drupal's temporary directory.
			// This overcomes open_basedir restrictions for future file operations.
			$file->filepath = $file->destination;
			if (!move_uploaded_file($_FILES['files']['tmp_name'][$source], $file->filepath)) {
				form_set_error($source, t('File upload error. Could not move uploaded file.'));
				watchdog('file', 'Upload error. Could not move uploaded file %file to destination %destination.', array('%file' => $file->filename, '%destination' => $file->filepath));
				return 0;
			}

			// If we made it this far it's safe to record this file in the database.
			$file->uid = $user->uid;
			$file->status = FILE_STATUS_TEMPORARY;
			$file->timestamp = time();
			drupal_write_record('files', $file);

			// Add file to the cache.
			$upload_cache[$source] = $file;
			return $file;
		}
		return 0;
	}


}
