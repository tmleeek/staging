<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MageBackup\Dropbox;

/**
 * MageBackup Dropbox Library.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class Dropbox {
	// Dropbox web endpoint
	const WEB_URL			= 'https://www.dropbox.com/1/';
	const API_URL			= 'https://api.dropbox.com/1/';
	const CONTENT_URL		= 'https://api-content.dropbox.com/1/';

	// OAuth flow methods
	const REQUEST_TOKEN_METHOD	= 'oauth/request_token';
	const AUTHORISE_METHOD		= 'oauth/authorize';
	const ACCESS_TOKEN_METHOD	= 'oauth/access_token';

	/** @var array Default cURL options */
	private $defaultOptions = array(
		CURLOPT_SSL_VERIFYPEER	=> false,
		CURLOPT_VERBOSE			=> true,
		CURLOPT_HEADER			=> true,
		CURLINFO_HEADER_OUT		=> false,
		CURLOPT_RETURNTRANSFER	=> true,
		//CURLOPT_FOLLOWLOCATION => true,
	);

	/** @var string Signature method, either PLAINTEXT or HMAC-SHA1 */
	private $sigMethod	= 'HMAC-SHA1';

	/** @var null|resource Output file handle */
	protected $outFile		= null;

	/** @var null|resource Input file handle */
	protected $inFile		= null;

	/** @var string Application public key */
	private $appKey			= '';

	/** @var string Application secret key */
	private $appSecret		= '';

	/** @var object Account token */
	private $token			= '';

	/** @var object Request token, sent to DropBox when requesting authorisation */
	private $requestToken	= '';

	private $chunkSize		= 4194304;

	/**
	 * The root level for file paths
	 * Either `dropbox` or `sandbox` (preferred)
	 * @var null|string
	 */
	private $root			= 'dropbox';

	/** @var string The response format, e.g. php, json, ... */
	private $responseFormat	= 'json';

	/**
	 * JSONP callback
	 * @var string
	 */
	private $callback		= 'dropboxCallback';

	/*
	 * =========================================================================
	 * GETTERS / SETTERS
	 * =========================================================================
	 */

	/**
	 * Sets the API keys
	 * @param object $keys
	 */
	public function setAppKeys($keys) {
		$this->appKey		= $keys->key;
		$this->appSecret	= $keys->secret;
	}

	/**
	 * Returns the API keys
	 * @return object
	 */
	public function getAppKeys() {
		$keys = (object)array(
			'key'		=> $this->appKey,
			'secret'	=> $this->appSecret
		);

		return $keys;
	}

	/**
	 * Sets the OAuth token
	 * @param object $token
	 */
	public function setToken($token) {
		$this->token	= $token;
	}

	/**
	 * Returns the OAuth token
	 * @return type object
	 */
	public function getToken() {
		if (!is_object($this->token)) {
			$this->token	= (object) array(
				'oauth_token'			=> null,
				'oauth_token_secret'	=> null
			);
		}

		return $this->token;
	}

	/**
	 * Sets the OAuth authorisation request token
	 * @param object $token
	 */
	public function setReqToken($token) {
		$this->requestToken = $token;
	}

	/**
	 * Returns the OAuth authorisation request token
	 * @return type object
	 */
	public function getReqToken() {
		if (!is_object($this->requestToken)) {
			$this->requestToken	= (object)array(
				'oauth_token'			=> null,
				'oauth_token_secret'	=> null
			);
		}

		return $this->requestToken;
	}

	/**
	 * Set the OAuth signature method
	 * @param string $method Either PLAINTEXT or HMAC-SHA1
	 * @return void
	 */
	public function setSignatureMethod($method) {
		$method = strtoupper($method);
		switch ($method) {
			case 'PLAINTEXT':
			case 'HMAC-SHA1':
				$this->sigMethod = $method;
				break;
			default:
				throw new \Exception('Unsupported signature method ' . $method);
		}
	}

	/**
	 * Set the API response format
	 * @param string $format One of php, json or jsonp
	 * @return void
	 */
	public function setResponseFormat($format) {
		$format = strtolower($format);
		if (!in_array($format, array('php', 'json', 'jsonp'))) {
			throw new \Exception("Expected a format of php, json or jsonp, got '$format'");
		} else {
			$this->responseFormat = $format;
		}
	}

	/**
	 * Return the API response format
	 * @return string
	 */
	public function getResponseFormat() {
		return $this->responseFormat;
	}

	/**
	 * Set the JSONP callback function
	 * @param string $function
	 * @return void
	 */
	public function setCallback($function) {
		$this->callback = $function;
	}

	/**
	 * Set the root level
	 * @param mixed $root
	 * @throws Exception
	 * @return void
	 */
	public function setRoot($root) {
		if ($root !== 'sandbox' && $root !== 'dropbox') {
			throw new \Exception("Expected a root of either 'dropbox' or 'sandbox', got '$root'");
		} else {
			$this->root = $root;
		}
	}

	/**
	 * Returns the root level (dropbox or sandbox)
	 *
	 * @return string Root level ("dropbox" or "sandbox")
	 */
	public function getRoot() {
		return $this->root;
	}

	/**
	 * Set the output file
	 * @return void
	 */
	public function setOutFile($handle) {
		if (!is_resource($handle) || get_resource_type($handle) != 'stream') {
			throw new \Exception('Outfile must be a stream resource');
		}
		$this->outFile = $handle;
	}

	/**
	 * Set the input file
	 * @return void
	 */
	public function setInFile($handle) {
		if (!is_resource($handle) || get_resource_type($handle) != 'stream') {
			throw new \Exception('Infile must be a stream resource');
		}
		fseek($handle, 0);
		$this->inFile = $handle;
	}


	/*
	 * =========================================================================
	 * PUBLIC API
	 * =========================================================================
	 */

	/**
	 * Returns an OAuth authorisation request URL. Remember to store the request
	 * token, fetching it with getReqToken(); it is required by getAccessToken()
	 * This is the first step of the three-step OAuth authentication flow
	 *
	 * @return string The URL to redirect the browser to
	 */
	public function getAuthoriseUrl() {
		$this->getRequestToken();

		// Prepare request parameters
		$params = array(
			'oauth_token'			=> $this->requestToken->oauth_token,
			'oauth_token_secret'	=> $this->requestToken->oauth_token_secret
		);

		// Build the URL and redirect the user
		$query = '?' . http_build_query($params, '', '&');
		$url = self::WEB_URL . self::AUTHORISE_METHOD . $query;

		return $url;
	}

	/**
	 * Fetches the access token (step 3 of OAuth authentication)
	 */
	public function getAccessToken() {
		$token		= $this->getReqToken();
		$this->setToken($token);
		$response	= $this->fetch('POST', self::API_URL, self::ACCESS_TOKEN_METHOD);
		$token		= $this->parseTokenString($response['body']);
		$this->setToken($token);
	}

	/**
	 * Retrieves information about the user's account
	 * @return object stdClass
	 */
	public function accountInfo() {
		$response	= $this->fetch('POST', self::API_URL, 'account/info');

		return $response;
	}

	/**
	 * Uploads a physical file from disk
	 * Dropbox impose a 150MB limit to files uploaded via the API. If the file
	 * exceeds this limit or does not exist, an Exception will be thrown
	 * @param string $file Absolute path to the file to be uploaded
	 * @param string|bool $filename The destination filename of the uploaded file
	 * @param string $path Path to upload the file to, relative to root
	 * @param boolean $overwrite Should the file be overwritten? (Default: true)
	 * @return object stdClass
	 */
	public function putFile($file, $filename = false, $path = '', $overwrite = true) {
		if (file_exists($file)) {
			if (filesize($file) <= 157286400) {
				$call = 'files/' . $this->root . '/' . $this->encodePath($path);
				// If no filename is provided we'll use the original filename
				$filename	= (is_string($filename)) ? $filename : basename($file);
				$params		= array(
					'filename'	=> $filename,
					'file'		=> '@' . str_replace('\\', '/', $file) . ';filename=' . $filename,
					'overwrite'	=> (int)$overwrite,
				);
				$response	= $this->fetch('POST', self::CONTENT_URL, $call, $params);

				return $response;
			}

			throw new \Exception('File exceeds 150MB upload limit');
		}

		// Throw an Exception if the file does not exist
		throw new \Exception('Local file ' . $file . ' does not exist');
	}

	/**
	 * Uploads file data from a stream
	 * Note: This function is experimental and requires further testing
	 *
	 * @param resource $stream A readable stream created using fopen()
	 * @param string $filename The destination filename, including path
	 * @param boolean $overwrite Should the file be overwritten? (Default: true)
	 * @return array
	 */
	public function putStream($stream, $filename, $overwrite = true) {
		$this->setInFile($stream);
		$path		= $this->encodePath($filename);
		$call		= 'files_put/' . $this->root . '/' . $path;
		$params		= array('overwrite' => (int)$overwrite);
		$response	= $this->fetch('PUT', self::CONTENT_URL, $call, $params);

		return $response;
	}

	/**
	 * Uploads large files to Dropbox in mulitple chunks
	 * @param string $file Absolute path to the file to be uploaded
	 * @param string|bool $filename The destination filename of the uploaded file
	 * @param string $path Path to upload the file to, relative to root
	 * @param boolean $overwrite Should the file be overwritten? (Default: true)
	 * @return stdClass
	 */
	public function chunkedUpload($file, $filename = false, $path = '', $overwrite = true, $offset = 0, $uploadID = null) {
		if (file_exists($file)) {
			if ($handle = @fopen($file, 'r')) {
				// Seek to the correct position on the file pointer
				fseek($handle, $offset);

				// Read from the file handle until EOF, uploading each chunk
				while ($data = fread($handle, $this->chunkSize)) {
					// Open a temporary file handle and write a chunk of data to it
					$chunkHandle = fopen('php://temp', 'rw');
					fwrite($chunkHandle, $data);

					// Set the file, request parameters and send the request
					$this->setInFile($chunkHandle);
					$params = array('upload_id' => $uploadID, 'offset' => $offset);

					try {
						// Attempt to upload the current chunk
						$response = $this->fetch('PUT', self::CONTENT_URL, 'chunked_upload', $params);
					} catch (Exception $e) {
						throw $e;
					}

					// On subsequent chunks, use the upload ID returned by the previous request
					if (isset($response['body']->upload_id)) {
						$uploadID = $response['body']->upload_id;
					}

					// Set the data offset
					if (isset($response['body']->offset)) {
						$offset = $response['body']->offset;
					}

					// Close the file handle for this chunk
					fclose($chunkHandle);
				}

				// Complete the chunked upload
				$filename	= (is_string($filename)) ? $filename : basename($file);
				$call		= 'commit_chunked_upload/' . $this->root . '/' . $this->encodePath(rtrim($path, '/') . '/' . $filename);
				$params		= array('overwrite' => (int) $overwrite, 'upload_id' => $uploadID);
				$response	= $this->fetch('POST', self::CONTENT_URL, $call, $params);

				return $response;
			} else {
				throw new \Exception('Could not open ' . $file . ' for reading');
			}
		}

		// Throw an Exception if the file does not exist
		throw new \Exception('Local file ' . $file . ' does not exist');
	}

	/**
	 * Downloads a file
	 * Returns the base filename, raw file data and mime type returned by Fileinfo
	 * @param string $file Path to file, relative to root, including path
	 * @param string $outFile Filename to write the downloaded file to
	 * @param string $revision The revision of the file to retrieve
	 * @return array
	 */
	public function getFile($file, $outFile = false, $revision = null) {
		// Only allow php response format for this call
		if ($this->responseFormat !== 'php') {
			throw new \Exception('This method only supports the `php` response format');
		}

		$handle = null;
		if ($outFile !== false) {
			// Create a file handle if $outFile is specified
			if (!$handle = fopen($outFile, 'w')) {
				throw new \Exception("Unable to open file handle for $outFile");
			} else {
				$this->setOutFile($handle);
			}
		}

		$file		= $this->encodePath($file);
		$call		= 'files/' . $this->root . '/' . $file;
		$params		= array('rev' => $revision);
		$response	= $this->fetch('GET', self::CONTENT_URL, $call, $params);

		// Close the file handle if one was opened
		if ($handle)
			fclose($handle);

		return array(
			'name'	=> ($outFile) ? $outFile : basename($file),
			'mime'	=> $this->getMimeType(($outFile) ? '' : $response['body'], $outFile),
			'meta'	=> json_decode($response['headers']['x-dropbox-metadata']),
			'data'	=> $response['body'],
		);
	}

	/**
	 * Retrieves file and folder metadata
	 * @param string $path The path to the file/folder, relative to root
	 * @param string $rev Return metadata for a specific revision (Default: latest rev)
	 * @param int $limit Maximum number of listings to return
	 * @param string $hash Metadata hash to compare against
	 * @param bool $list Return contents field with response
	 * @param bool $deleted Include files/folders that have been deleted
	 * @return object stdClass
	 */
	public function metaData($path = null, $rev = null, $limit = 10000, $hash = false, $list = true, $deleted = false) {
		$call	= 'metadata/' . $this->root . '/' . $this->encodePath($path);
		$params	= array(
			'file_limit'		=> ($limit < 1) ? 1 : (($limit > 10000) ? 10000 : (int)$limit),
			'hash'				=> (is_string($hash)) ? $hash : 0,
			'list'				=> (int)$list,
			'include_deleted'	=> (int)$deleted,
			'rev'				=> (is_string($rev)) ? $rev : null,
		);

		$response = $this->fetch('POST', self::API_URL, $call, $params);

		return $response;
	}

	/**
	 * Obtains metadata for the previous revisions of a file
	 * @param string Path to the file, relative to root
	 * @param integer Number of revisions to return (1-1000)
	 * @return array
	 */
	public function revisions($file, $limit = 10) {
		$call	= 'revisions/' . $this->root . '/' . $this->encodePath($file);
		$params	= array(
			'rev_limit'	=> ($limit < 1) ? 1 : (($limit > 1000) ? 1000 : (int)$limit),
		);
		$response	= $this->fetch('GET', self::API_URL, $call, $params);

		return $response;
	}

	/**
	 * Restores a file path to a previous revision
	 * @param string $file Path to the file, relative to root
	 * @param string $revision The revision of the file to restore
	 * @return object stdClass
	 */
	public function restore($file, $revision) {
		$call		= 'restore/' . $this->root . '/' . $this->encodePath($file);
		$params		= array('rev' => $revision);
		$response	= $this->fetch('POST', self::API_URL, $call, $params);

		return $response;
	}

	/**
	 * Returns metadata for all files and folders that match the search query
	 * @param mixed $query The search string. Must be at least 3 characters long
	 * @param string $path The path to the folder you want to search in
	 * @param integer $limit Maximum number of results to return (1-1000)
	 * @param boolean $deleted Include deleted files/folders in the search
	 * @return array
	 */
	public function search($query, $path = '', $limit = 1000, $deleted = false) {
		$call	= 'search/' . $this->root . '/' . $this->encodePath($path);
		$params	= array(
			'query'				=> $query,
			'file_limit'		=> ($limit < 1) ? 1 : (($limit > 1000) ? 1000 : (int)$limit),
			'include_deleted'	=> (int)$deleted,
		);

		$response	= $this->fetch('GET', self::API_URL, $call, $params);

		return $response;
	}

	/**
	 * Creates and returns a shareable link to files or folders
	 * @param string $path The path to the file/folder you want a sharable link to
	 * @return object stdClass
	 */
	public function shares($path) {
		$call		= 'shares/' . $this->root . '/' . $this->encodePath($path);
		$response	= $this->fetch('POST', self::API_URL, $call);

		return $response;
	}

	/**
	 * Returns a link directly to a file
	 * @param string $path The path to the media file you want a direct link to
	 * @return object stdClass
	 */
	public function media($path) {
		$call		= 'media/' . $this->root . '/' . $this->encodePath($path);
		$response	= $this->fetch('POST', self::API_URL, $call);

		return $response;
	}

	/**
	 * Gets a thumbnail for an image
	 * @param string $file The path to the image you wish to thumbnail
	 * @param string $format The thumbnail format, either JPEG or PNG
	 * @param string $size The size of the thumbnail
	 * @return array
	 */
	public function thumbnails($file, $format = 'JPEG', $size = 'small') {
		// Only allow php response format for this call
		if ($this->responseFormat !== 'php') {
			throw new \Exception('This method only supports the `php` response format');
		}

		$format = strtoupper($format);
		// If $format is not 'PNG', default to 'JPEG'
		if ($format != 'PNG')
			$format = 'JPEG';

		$size	= strtolower($size);
		$sizes	= array('s', 'm', 'l', 'xl', 'small', 'medium', 'large');
		// If $size is not valid, default to 'small'
		if (!in_array($size, $sizes))
			$size = 'small';

		$call		= 'thumbnails/' . $this->root . '/' . $this->encodePath($file);
		$params		= array('format' => $format, 'size' => $size);
		$response	= $this->fetch('GET', self::CONTENT_URL, $call, $params);

		return array(
			'name'	=> basename($file),
			'mime'	=> $this->getMimeType($response['body']),
			'meta'	=> json_decode($response['headers']['x-dropbox-metadata']),
			'data'	=> $response['body'],
		);
	}

	/**
	 * Copies a file or folder to a new location
	 * @param string $from File or folder to be copied, relative to root
	 * @param string $to Destination path, relative to root
	 * @return object stdClass
	 */
	public function copy($from, $to) {
		$call	= 'fileops/copy';
		$params	= array(
			'root'		=> $this->root,
			'from_path'	=> $this->normalisePath($from),
			'to_path'	=> $this->normalisePath($to),
		);

		$response	= $this->fetch('POST', self::API_URL, $call, $params);

		return $response;
	}

	/**
	 * Creates a folder
	 * @param string New folder to create relative to root
	 * @return object stdClass
	 */
	public function create($path) {
		$call		= 'fileops/create_folder';
		$params		= array('root' => $this->root, 'path' => $this->normalisePath($path));
		$response	= $this->fetch('POST', self::API_URL, $call, $params);

		return $response;
	}

	/**
	 * Deletes a file or folder
	 * @param string $path The path to the file or folder to be deleted
	 * @return object stdClass
	 */
	public function delete($path) {
		$call		= 'fileops/delete';
		$params		= array('root' => $this->root, 'path' => $this->normalisePath($path));
		$response	= $this->fetch('POST', self::API_URL, $call, $params);

		return $response;
	}

	/**
	 * Moves a file or folder to a new location
	 * @param string $from File or folder to be moved, relative to root
	 * @param string $to Destination path, relative to root
	 * @return object stdClass
	 */
	public function move($from, $to) {
		$call	= 'fileops/move';
		$params	= array(
			'root'		=> $this->root,
			'from_path'	=> $this->normalisePath($from),
			'to_path'	=> $this->normalisePath($to),
		);
		$response =	$this->fetch('POST', self::API_URL, $call, $params);

		return $response;
	}

	/*
	 * =========================================================================
	 * PRIVATE API
	 * =========================================================================
	 */

	/**
	 * Acquire an unauthorised request token
	 * @link http://tools.ietf.org/html/rfc5849#section-2.1
	 * @return void
	 */
	private function getRequestToken() {
		$url				= self::API_URL . self::REQUEST_TOKEN_METHOD;
		$response			= $this->fetch('POST', $url, '');
		$token				= $this->parseTokenString($response['body']);
		$this->requestToken	= $token;
	}

	/**
	 * Execute an API call
	 *
	 * @param string $method The HTTP method
	 * @param string $url The API endpoint
	 * @param string $call The API method to call
	 * @param array $params Additional parameters
	 * @return string|object stdClass
	 */
	private function fetch($method, $url, $call, array $additional = array()) {
		// Get the signed request URL
		$request 	= $this->getSignedRequest($method, $url, $call, $additional);

		// Initialise and execute a cURL request
		$ch			= curl_init($request['url']);

		// Get the default options array
		$options	= $this->defaultOptions;

		if ($method == 'GET' && $this->outFile) { // GET
			$options[CURLOPT_RETURNTRANSFER]	= false;
			$options[CURLOPT_HEADER]			= false;
			$options[CURLOPT_FILE]				= $this->outFile;
			$options[CURLOPT_BINARYTRANSFER]	= true;
			$this->outFile = null;
		} elseif ($method == 'POST') { // POST
			$options[CURLOPT_POST]				= true;
			$options[CURLOPT_POSTFIELDS]		= $request['postfields'];
		} elseif ($method == 'PUT' && $this->inFile) { // PUT
			$options[CURLOPT_PUT]				= true;
			$options[CURLOPT_INFILE]			= $this->inFile;
			// @todo Update so the data is not loaded into memory to get its size
			$options[CURLOPT_INFILESIZE]		= strlen(stream_get_contents($this->inFile));
			fseek($this->inFile, 0);
			$this->inFile						= null;
		}

		// Set the cURL options at once
		curl_setopt_array($ch, $options);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		// Execute and parse the response
		$response = curl_exec($ch);
		curl_close($ch);

		// Parse the response if it is a string
		if (is_string($response)) {
			$response = $this->parse($response);
		}

		// Check if an error occurred and throw an Exception
		if (!empty($response['body']->error)) {
			$message = $response['body']->error . ' (Status Code: ' . $response['code'] . ')';
			throw new \Exception($message);
		}

		return $response;
	}

	/**
	 * Parse a cURL response
	 * @param string $response
	 * @return array
	 */
	private function parse($response) {
		// Explode the response into headers and body parts (separated by double EOL)
		list($headers, $response)	= explode("\r\n\r\n", $response, 2);

		// Explode response headers
		$lines	= explode("\r\n", $headers);

		// If the status code is 100, the API server must send a final response
		// We need to explode the response again to get the actual response
		if (preg_match('#^HTTP/1.1 100#', $lines[0])) {
			list($headers, $response) = explode("\r\n\r\n", $response, 2);
			$lines = explode("\r\n", $headers);
		}

		// Get the HTTP response code from the first line
		$first		= array_shift($lines);
		$pattern	= '#^HTTP/1.1 ([0-9]{3})#';
		preg_match($pattern, $first, $matches);
		$code		= $matches[1];

		// Parse the remaining headers into an associative array
		$headers	= array();
		foreach ($lines as $line) {
			list($k, $v) = explode(': ', $line, 2);
			$headers[strtolower($k)] = $v;
		}

		// If the response body is not a JSON encoded string
		// we'll return the entire response body
		if (!$body = json_decode($response)) {
			$body	= $response;
		}

		return array('code' => $code, 'body' => $body, 'headers' => $headers);
	}

	/**
	 * Generate signed request URL
	 */
	private function getSignedRequest($method, $url, $call, array $additional = array()) {
		$token	= $this->getToken();

		// Generate a random string for the request
		$nonce	= md5(microtime(true) . uniqid('', true));

		// Prepare the standard request parameters
		$params = array(
			'oauth_consumer_key'		=> $this->appKey,
			'oauth_token'				=> $token->oauth_token,
			'oauth_signature_method'	=> $this->sigMethod,
			'oauth_version'				=> '1.0',
			// Generate nonce and timestamp if signature method is HMAC-SHA1
			'oauth_timestamp'			=> ($this->sigMethod == 'HMAC-SHA1') ? time() : null,
			'oauth_nonce'				=> ($this->sigMethod == 'HMAC-SHA1') ? $nonce : null,
		);

		// Merge with the additional request parameters
		$params = array_merge($params, $additional);
		ksort($params);

		// URL encode each parameter to RFC3986 for use in the base string
		$encoded = array();
		foreach ($params as $param => $value) {
			if ($value !== null) {
				// If the value is a file upload (prefixed with @), replace it with
				// the destination filename, the file path will be sent in POSTFIELDS
				if ($value[0] === '@') {
					$value	= $params['filename'];
				}

				$encoded[]	= $this->encode($param) . '=' . $this->encode($value);
			} else {
				unset($params[$param]);
			}
		}

		// Build the first part of the string
		$base	= $method . '&' . $this->encode($url . $call) . '&';

		// Re-encode the encoded parameter string and append to $base
		$base	.= $this->encode(implode('&', $encoded));

		// Concatenate the secrets with an ampersand
		$key	= $this->appSecret . '&' . $token->oauth_token_secret;

		// Get the signature string based on signature method
		$signature	= $this->getSignature($base, $key);
		$params['oauth_signature']	= $signature;

		// Build the signed request URL
		$query	= '?' . http_build_query($params, '', '&');

		return array(
			'url'			=> $url . $call . $query,
			'postfields'	=> $params,
		);
	}

	/**
	 * Generate the oauth_signature for a request
	 * @param string $base Signature base string, used by HMAC-SHA1
	 * @param string $key Concatenated consumer and token secrets
	 */
	private function getSignature($base, $key) {
		switch ($this->sigMethod) {
			case 'PLAINTEXT':
				$signature = $key;
				break;
			case 'HMAC-SHA1':
				//$hash = hash_hmac('sha1', $base, $key, true);
				$hash	= pack('H*', sha1(
					(str_pad($key, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
					pack('H*', sha1((str_pad($key, 64, chr(0x00)) ^
								(str_repeat(chr(0x36), 64))) . $base))));
				$signature	= base64_encode($hash);
				break;
		}

		return $signature;
	}


	/**
	 * Parse response parameters for a token into an object
	 * Dropbox returns tokens in the response parameters, and
	 * not a JSON encoded object as per other API requests
	 * @link http://oauth.net/core/1.0/#response_parameters
	 * @param string $response
	 * @return object stdClass
	 */
	private function parseTokenString($response) {
		$parts	= explode('&', $response);
		$token	= new \stdClass();

		foreach ($parts as $part) {
			list($k, $v)	= explode('=', $part, 2);
			$k				= strtolower($k);
			$token->$k		= $v;
		}

		return $token;
	}

	/**
	 * Encode a value to RFC3986
	 * This is a convenience method to decode ~ symbols encoded
	 * by rawurldecode. This will encode all characters except
	 * the unreserved set, ALPHA, DIGIT, '-', '.', '_', '~'
	 * @link http://tools.ietf.org/html/rfc5849#section-3.6
	 * @param mixed $value
	 */
	private function encode($value) {
		return str_replace('%7E', '~', rawurlencode($value));
	}

	/**
	 * Get the mime type of downloaded file
	 * If the Fileinfo extension is not loaded, return false
	 * @param string $data File contents as a string or filename
	 * @param string $isFilename Is $data a filename?
	 * @return boolean|string Mime type and encoding of the file
	 */
	private function getMimeType($data, $isFilename = false) {
		if (extension_loaded('fileinfo')) {
			$finfo = new finfo(FILEINFO_MIME);
			if ($isFilename !== false)
				return $finfo->file($data);

			return $finfo->buffer($data);
		}

		return false;
	}

	/**
	 * Trim the path of forward slashes and replace
	 * consecutive forward slashes with a single slash
	 * @param string $path The path to normalise
	 * @return string
	 */
	private function normalisePath($path) {
		$path	= preg_replace('#/+#', '/', trim($path, '/'));

		return $path;
	}

	/**
	 * Encode the path, then replace encoded slashes
	 * with literal forward slash characters
	 * @param string $path The path to encode
	 * @return string
	 */
	private function encodePath($path) {
		$path	= $this->normalisePath($path);
		$path	= str_replace('%2F', '/', rawurlencode($path));

		return $path;
	}
}