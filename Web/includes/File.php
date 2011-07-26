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
	 * @defgroup file_error
	 * @{
	 * Define a set of error messages
	 */
	const ERROR_SUCCEED         = 'File is successfully uploaded';
	const ERROR_FAIL            = 'File could not be saved';
	const ERROR_EXCEED_MAX_SIZE = 'File could not be saved because it exceeds the maximum allowed size for uploads';
	const ERROR_FILE_RENAMED    = 'File renamed for security purpose';
	/**
	 * @} End of group "file_error"
	 */
	
	/**
	 * @defgroup file_event
	 * @{
	 * Definet a set of event messages
	 */
	const EVENT_SUCCEED         = 'File is successfully uploaded';
	const EVENT_FAIL            = 'File could not be saved';
	const EVENT_EXCEED_MAX_SIZE = 'File could not be save because it exceeds the maximum allowed size for uploads';
	const EVENT_FILE_RENAMED    = 'File renamed for security purpose';
	/**
	 * @} End of group "file_event"
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
	 * @param $dest
	 *   A string containing the directory $source should be copied to. If this is
	 *   not provided or is not writable, the default directory will be used.
	 * @param $replace
	 *   A boolean indicating whether an existing file of the same name in the
	 *   destination directory should overwritten. A false value will generate a
	 *   new, unique filename in the destination directory.
	 * @return
	 *  An associative array that has two different results.
	 *  On success:
	 *   - name
	 *   - encoded
	 *   - size
	 *   - path
	 *   - mime
	 *   - timestamp
	 *  On faie:
	 *   - error
	 */
	public static function SaveUpload($source, $dest = FALSE, $replace = self::EXISTS_RENAME) {
		static $upload_cache;

		// Return cached objects without processing since the file will have
		// already been processed and the paths in _FILES will be invalid.
		if (isset($upload_cache[$source])) {
			return $upload_cache[$source];
		}

		// If a file was uploaded, process it.
		if (isset($_FILES[$source]) && $_FILES[$source]['name'] && is_uploaded_file($_FILES[$source]['tmp_name'])) {
			// Check for file upload errors and return FALSE if a
			// lower level system error occurred.
			switch ($_FILES[$source]['error']) {
				// @see http://php.net/manual/en/features.file-upload.errors.php
				case UPLOAD_ERR_OK:
					break;

				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					Logger::Write(self::EVENT_EXCEED_MAX_SIZE);
					return array('error' => self::ERROR_EXCEED_MAX_SIZE);

				case UPLOAD_ERR_PARTIAL:
				case UPLOAD_ERR_NO_FILE:
				// Unknown error
				default:
					Logger::Write(self::EVENT_FAIL, Logger::SEVERITY_LOW);
					return array('error' => self::ERROR_FAIL);
			}

			// Begin building file object.
			$file['name']    = $_FILES[$source]['name'];
			$file['size']    = $_FILES[$source]['size'];
			$file['encoded'] = preg_replace('/\W/', '-', Crypto::Encrypt($file['name'] . time()));
			$file['path']    = FILE_PATH . '/' . $file['encoded'];
			$file['mime']    = self::getMimeType($_FILES[$source]['name']);

			// Move uploaded files from PHP's upload_tmp_dir
			// This overcomes open_basedir restrictions for future file operations.
			if (!move_uploaded_file($_FILES[$source]['tmp_name'], $file['path'])) {
				Logger::Write(self::EVENT_FAIL, Logger::SEVERITY_LOW);
				return array('error' => self::ERROR_FAIL);
			}

			// If we made it this far it's safe to record this file in the database.
			$file['timestamp'] = time();

			// Add file to the cache.
			$upload_cache[$source] = $file;
			return $file;
		}
		Logger::Write(self::EVENT_FAIL, Logger::SEVERITY_LOW);
		return array('error' => self::ERROR_FAIL);
	}

	/**
	 * Determine an Internet Media Type, or MIME type from a filename.
	 *
	 * @param $filename
	 *  Name of the file, including extension.
	 * @param $mapping
	 *  An optional array of extension to media type mappings in the form
	 *  'extension1|extension2|...' => 'type'.
	 *
	 * @return
	 *  The internet media type registered for the extension or 
	 *  application/octet-stream for unknown extensions.
	 */
	public static function getMimeType($filename, $mapping = NULL) {
		if (!is_array($mapping)) {
			$mapping = array(
				'ez' => 'application/andrew-inset',
				'atom' => 'application/atom',
				'atomcat' => 'application/atomcat+xml',
				'atomsrv' => 'application/atomserv+xml',
				'cap|pcap' => 'application/cap',
				'cu' => 'application/cu-seeme',
				'tsp' => 'application/dsptype',
				'spl' => 'application/x-futuresplash',
				'hta' => 'application/hta',
				'jar' => 'application/java-archive',
				'ser' => 'application/java-serialized-object',
				'class' => 'application/java-vm',
				'hqx' => 'application/mac-binhex40',
				'cpt' => 'image/x-corelphotopaint',
				'nb' => 'application/mathematica',
				'mdb' => 'application/msaccess',
				'doc|dot' => 'application/msword',
				'bin' => 'application/octet-stream',
				'oda' => 'application/oda',
				'ogg|ogx' => 'application/ogg',
				'pdf' => 'application/pdf',
				'key' => 'application/pgp-keys',
				'pgp' => 'application/pgp-signature',
				'prf' => 'application/pics-rules',
				'ps|ai|eps' => 'application/postscript',
				'rar' => 'application/rar',
				'rdf' => 'application/rdf+xml',
				'rss' => 'application/rss+xml',
				'rtf' => 'application/rtf',
				'smi|smil' => 'application/smil',
				'wpd' => 'application/wordperfect',
				'wp5' => 'application/wordperfect5.1',
				'xhtml|xht' => 'application/xhtml+xml',
				'xml|xsl' => 'application/xml',
				'zip' => 'application/zip',
				'cdy' => 'application/vnd.cinderella',
				'kml' => 'application/vnd.google-earth.kml+xml',
				'kmz' => 'application/vnd.google-earth.kmz',
				'xul' => 'application/vnd.mozilla.xul+xml',
				'xls|xlb|xlt' => 'application/vnd.ms-excel',
				'cat' => 'application/vnd.ms-pki.seccat',
				'stl' => 'application/vnd.ms-pki.stl',
				'ppt|pps' => 'application/vnd.ms-powerpoint',
				'odc' => 'application/vnd.oasis.opendocument.chart',
				'odb' => 'application/vnd.oasis.opendocument.database',
				'odf' => 'application/vnd.oasis.opendocument.formula',
				'odg' => 'application/vnd.oasis.opendocument.graphics',
				'otg' => 'application/vnd.oasis.opendocument.graphics-template',
				'odi' => 'application/vnd.oasis.opendocument.image',
				'odp' => 'application/vnd.oasis.opendocument.presentation',
				'otp' => 'application/vnd.oasis.opendocument.presentation-template',
				'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
				'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
				'odt' => 'application/vnd.oasis.opendocument.text',
				'odm' => 'application/vnd.oasis.opendocument.text-master',
				'ott' => 'application/vnd.oasis.opendocument.text-template',
				'oth' => 'application/vnd.oasis.opendocument.text-web',
				'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
				'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
				'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
				'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
				'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
				'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
				'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
				'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
				'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
				'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
				'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
				'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
				'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
				'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
				'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
				'cod' => 'application/vnd.rim.cod',
				'mmf' => 'application/vnd.smaf',
				'sdc' => 'application/vnd.stardivision.calc',
				'sds' => 'application/vnd.stardivision.chart',
				'sda' => 'application/vnd.stardivision.draw',
				'sdd' => 'application/vnd.stardivision.impress',
				'sdf' => 'application/vnd.stardivision.math',
				'sdw' => 'application/vnd.stardivision.writer',
				'sgl' => 'application/vnd.stardivision.writer-global',
				'sxc' => 'application/vnd.sun.xml.calc',
				'stc' => 'application/vnd.sun.xml.calc.template',
				'sxd' => 'application/vnd.sun.xml.draw',
				'std' => 'application/vnd.sun.xml.draw.template',
				'sxi' => 'application/vnd.sun.xml.impress',
				'sti' => 'application/vnd.sun.xml.impress.template',
				'sxm' => 'application/vnd.sun.xml.math',
				'sxw' => 'application/vnd.sun.xml.writer',
				'sxg' => 'application/vnd.sun.xml.writer.global',
				'stw' => 'application/vnd.sun.xml.writer.template',
				'sis' => 'application/vnd.symbian.install',
				'vsd' => 'application/vnd.visio',
				'wbxml' => 'application/vnd.wap.wbxml',
				'wmlc' => 'application/vnd.wap.wmlc',
				'wmlsc' => 'application/vnd.wap.wmlscriptc',
				'wk' => 'application/x-123',
				'7z' => 'application/x-7z-compressed',
				'abw' => 'application/x-abiword',
				'dmg' => 'application/x-apple-diskimage',
				'bcpio' => 'application/x-bcpio',
				'torrent' => 'application/x-bittorrent',
				'cab' => 'application/x-cab',
				'cbr' => 'application/x-cbr',
				'cbz' => 'application/x-cbz',
				'cdf' => 'application/x-cdf',
				'vcd' => 'application/x-cdlink',
				'pgn' => 'application/x-chess-pgn',
				'cpio' => 'application/x-cpio',
				'csh' => 'text/x-csh',
				'deb|udeb' => 'application/x-debian-package',
				'dcr|dir|dxr' => 'application/x-director',
				'dms' => 'application/x-dms',
				'wad' => 'application/x-doom',
				'dvi' => 'application/x-dvi',
				'rhtml' => 'application/x-httpd-eruby',
				'flac' => 'application/x-flac',
				'pfa|pfb|gsf|pcf|pcf.Z' => 'application/x-font',
				'mm' => 'application/x-freemind',
				'gnumeric' => 'application/x-gnumeric',
				'sgf' => 'application/x-go-sgf',
				'gcf' => 'application/x-graphing-calculator',
				'gtar|tgz|taz' => 'application/x-gtar',
				'hdf' => 'application/x-hdf',
				'phtml|pht|php' => 'application/x-httpd-php',
				'phps' => 'application/x-httpd-php-source',
				'php3' => 'application/x-httpd-php3',
				'php3p' => 'application/x-httpd-php3-preprocessed',
				'php4' => 'application/x-httpd-php4',
				'ica' => 'application/x-ica',
				'ins|isp' => 'application/x-internet-signup',
				'iii' => 'application/x-iphone',
				'iso' => 'application/x-iso9660-image',
				'jnlp' => 'application/x-java-jnlp-file',
				'js' => 'application/x-javascript',
				'jmz' => 'application/x-jmol',
				'chrt' => 'application/x-kchart',
				'kil' => 'application/x-killustrator',
				'skp|skd|skt|skm' => 'application/x-koan',
				'kpr|kpt' => 'application/x-kpresenter',
				'ksp' => 'application/x-kspread',
				'kwd|kwt' => 'application/x-kword',
				'latex' => 'application/x-latex',
				'lha' => 'application/x-lha',
				'lyx' => 'application/x-lyx',
				'lzh' => 'application/x-lzh',
				'lzx' => 'application/x-lzx',
				'frm|maker|frame|fm|fb|book|fbdoc' => 'application/x-maker',
				'mif' => 'application/x-mif',
				'wmd' => 'application/x-ms-wmd',
				'wmz' => 'application/x-ms-wmz',
				'com|exe|bat|dll' => 'application/x-msdos-program',
				'msi' => 'application/x-msi',
				'nc' => 'application/x-netcdf',
				'pac' => 'application/x-ns-proxy-autoconfig',
				'nwc' => 'application/x-nwc',
				'o' => 'application/x-object',
				'oza' => 'application/x-oz-application',
				'p7r' => 'application/x-pkcs7-certreqresp',
				'crl' => 'application/x-pkcs7-crl',
				'pyc|pyo' => 'application/x-python-code',
				'qtl' => 'application/x-quicktimeplayer',
				'rpm' => 'application/x-redhat-package-manager',
				'sh' => 'text/x-sh',
				'shar' => 'application/x-shar',
				'swf|swfl' => 'application/x-shockwave-flash',
				'sit|sitx' => 'application/x-stuffit',
				'sv4cpio' => 'application/x-sv4cpio',
				'sv4crc' => 'application/x-sv4crc',
				'tar' => 'application/x-tar',
				'tcl' => 'application/x-tcl',
				'gf' => 'application/x-tex-gf',
				'pk' => 'application/x-tex-pk',
				'texinfo|texi' => 'application/x-texinfo',
				'~|%|bak|old|sik' => 'application/x-trash',
				't|tr|roff' => 'application/x-troff',
				'man' => 'application/x-troff-man',
				'me' => 'application/x-troff-me',
				'ms' => 'application/x-troff-ms',
				'ustar' => 'application/x-ustar',
				'src' => 'application/x-wais-source',
				'wz' => 'application/x-wingz',
				'crt' => 'application/x-x509-ca-cert',
				'xcf' => 'application/x-xcf',
				'fig' => 'application/x-xfig',
				'xpi' => 'application/x-xpinstall',
				'au|snd' => 'audio/basic',
				'mid|midi|kar' => 'audio/midi',
				'mpga|mpega|mp2|mp3|m4a' => 'audio/mpeg',
				'f4a|f4b' => 'audio/mp4',
				'm3u' => 'audio/x-mpegurl',
				'oga|spx' => 'audio/ogg',
				'sid' => 'audio/prs.sid',
				'aif|aiff|aifc' => 'audio/x-aiff',
				'gsm' => 'audio/x-gsm',
				'wma' => 'audio/x-ms-wma',
				'wax' => 'audio/x-ms-wax',
				'ra|rm|ram' => 'audio/x-pn-realaudio',
				'ra' => 'audio/x-realaudio',
				'pls' => 'audio/x-scpls',
				'sd2' => 'audio/x-sd2',
				'wav' => 'audio/x-wav',
				'alc' => 'chemical/x-alchemy',
				'cac|cache' => 'chemical/x-cache',
				'csf' => 'chemical/x-cache-csf',
				'cbin|cascii|ctab' => 'chemical/x-cactvs-binary',
				'cdx' => 'chemical/x-cdx',
				'cer' => 'chemical/x-cerius',
				'c3d' => 'chemical/x-chem3d',
				'chm' => 'chemical/x-chemdraw',
				'cif' => 'chemical/x-cif',
				'cmdf' => 'chemical/x-cmdf',
				'cml' => 'chemical/x-cml',
				'cpa' => 'chemical/x-compass',
				'bsd' => 'chemical/x-crossfire',
				'csml|csm' => 'chemical/x-csml',
				'ctx' => 'chemical/x-ctx',
				'cxf|cef' => 'chemical/x-cxf',
				'emb|embl' => 'chemical/x-embl-dl-nucleotide',
				'spc' => 'chemical/x-galactic-spc',
				'inp|gam|gamin' => 'chemical/x-gamess-input',
				'fch|fchk' => 'chemical/x-gaussian-checkpoint',
				'cub' => 'chemical/x-gaussian-cube',
				'gau|gjc|gjf' => 'chemical/x-gaussian-input',
				'gal' => 'chemical/x-gaussian-log',
				'gcg' => 'chemical/x-gcg8-sequence',
				'gen' => 'chemical/x-genbank',
				'hin' => 'chemical/x-hin',
				'istr|ist' => 'chemical/x-isostar',
				'jdx|dx' => 'chemical/x-jcamp-dx',
				'kin' => 'chemical/x-kinemage',
				'mcm' => 'chemical/x-macmolecule',
				'mmd|mmod' => 'chemical/x-macromodel-input',
				'mol' => 'chemical/x-mdl-molfile',
				'rd' => 'chemical/x-mdl-rdfile',
				'rxn' => 'chemical/x-mdl-rxnfile',
				'sd|sdf' => 'chemical/x-mdl-sdfile',
				'tgf' => 'chemical/x-mdl-tgf',
				'mcif' => 'chemical/x-mmcif',
				'mol2' => 'chemical/x-mol2',
				'b' => 'chemical/x-molconn-Z',
				'gpt' => 'chemical/x-mopac-graph',
				'mop|mopcrt|mpc|dat|zmt' => 'chemical/x-mopac-input',
				'moo' => 'chemical/x-mopac-out',
				'mvb' => 'chemical/x-mopac-vib',
				'asn' => 'chemical/x-ncbi-asn1-spec',
				'prt|ent' => 'chemical/x-ncbi-asn1-ascii',
				'val|aso' => 'chemical/x-ncbi-asn1-binary',
				'pdb|ent' => 'chemical/x-pdb',
				'ros' => 'chemical/x-rosdal',
				'sw' => 'chemical/x-swissprot',
				'vms' => 'chemical/x-vamas-iso14976',
				'vmd' => 'chemical/x-vmd',
				'xtel' => 'chemical/x-xtel',
				'xyz' => 'chemical/x-xyz',
				'gif' => 'image/gif',
				'ief' => 'image/ief',
				'jpeg|jpg|jpe' => 'image/jpeg',
				'pcx' => 'image/pcx',
				'png' => 'image/png',
				'svg|svgz' => 'image/svg+xml',
				'tiff|tif' => 'image/tiff',
				'djvu|djv' => 'image/vnd.djvu',
				'wbmp' => 'image/vnd.wap.wbmp',
				'ras' => 'image/x-cmu-raster',
				'cdr' => 'image/x-coreldraw',
				'pat' => 'image/x-coreldrawpattern',
				'cdt' => 'image/x-coreldrawtemplate',
				'ico' => 'image/x-icon',
				'art' => 'image/x-jg',
				'jng' => 'image/x-jng',
				'bmp' => 'image/x-ms-bmp',
				'psd' => 'image/x-photoshop',
				'pnm' => 'image/x-portable-anymap',
				'pbm' => 'image/x-portable-bitmap',
				'pgm' => 'image/x-portable-graymap',
				'ppm' => 'image/x-portable-pixmap',
				'rgb' => 'image/x-rgb',
				'xbm' => 'image/x-xbitmap',
				'xpm' => 'image/x-xpixmap',
				'xwd' => 'image/x-xwindowdump',
				'eml' => 'message/rfc822',
				'igs|iges' => 'model/iges',
				'msh|mesh|silo' => 'model/mesh',
				'wrl|vrml' => 'model/vrml',
				'ics|icz' => 'text/calendar',
				'css' => 'text/css',
				'csv' => 'text/csv',
				'323' => 'text/h323',
				'html|htm|shtml' => 'text/html',
				'uls' => 'text/iuls',
				'mml' => 'text/mathml',
				'asc|txt|text|pot' => 'text/plain',
				'rtx' => 'text/richtext',
				'sct|wsc' => 'text/scriptlet',
				'tm|ts' => 'text/texmacs',
				'tsv' => 'text/tab-separated-values',
				'jad' => 'text/vnd.sun.j2me.app-descriptor',
				'wml' => 'text/vnd.wap.wml',
				'wmls' => 'text/vnd.wap.wmlscript',
				'bib' => 'text/x-bibtex',
				'boo' => 'text/x-boo',
				'h++|hpp|hxx|hh' => 'text/x-c++hdr',
				'c++|cpp|cxx|cc' => 'text/x-c++src',
				'h' => 'text/x-chdr',
				'htc' => 'text/x-component',
				'c' => 'text/x-csrc',
				'd' => 'text/x-dsrc',
				'diff|patch' => 'text/x-diff',
				'hs' => 'text/x-haskell',
				'java' => 'text/x-java',
				'lhs' => 'text/x-literate-haskell',
				'moc' => 'text/x-moc',
				'p|pas' => 'text/x-pascal',
				'gcd' => 'text/x-pcs-gcd',
				'pl|pm' => 'text/x-perl',
				'py' => 'text/x-python',
				'etx' => 'text/x-setext',
				'tcl|tk' => 'text/x-tcl',
				'tex|ltx|sty|cls' => 'text/x-tex',
				'vcs' => 'text/x-vcalendar',
				'vcf' => 'text/x-vcard',
				'3gp' => 'video/3gpp',
				'dl' => 'video/dl',
				'dif|dv' => 'video/dv',
				'fli' => 'video/fli',
				'gl' => 'video/gl',
				'mpeg|mpg|mpe' => 'video/mpeg',
				'mp4|f4v|f4p' => 'video/mp4',
				'flv' => 'video/x-flv',
				'ogv' => 'video/ogg',
				'qt|mov' => 'video/quicktime',
				'mxu' => 'video/vnd.mpegurl',
				'lsf|lsx' => 'video/x-la-asf',
				'mng' => 'video/x-mng',
				'asf|asx' => 'video/x-ms-asf',
				'wm' => 'video/x-ms-wm',
				'wmv' => 'video/x-ms-wmv',
				'wmx' => 'video/x-ms-wmx',
				'wvx' => 'video/x-ms-wvx',
				'avi' => 'video/x-msvideo',
				'movie' => 'video/x-sgi-movie',
				'ice' => 'x-conference/x-cooltalk',
				'sisx' => 'x-epoc/x-sisx-app',
				'vrm|vrml|wrl' => 'x-world/x-vrml',
				'xps' => 'application/vnd.ms-xpsdocument',
			);
		}
		foreach ($mapping as $ext_preg => $mime_match) {
			if (preg_match('!\.('. $ext_preg .')$!i', $filename)) {
				return $mime_match;
			}
		}

		return 'application/octet-stream';
	}


}
