<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MageBackup\OneDrive;

/**
 * MageBackup Dropbox Library.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class OneDrive {
	/**
	 * @var	string	The base URL for API request.
	 */
	const API_URL	= 'https://api.onedrive.com/v1.0/';

	/**
	 * @var	string	The base URL for authorization requests.
	 */
	const AUTH_URL	= 'https://login.live.com/oauth20_authorize.srf';

	/**
	 * @var	string	The base URL for token requests.
	 */
	const TOKEN_URL	= 'https://login.live.com/oauth20_token.srf';

	/**
	 * @var	string	The application ID.
	 */
	private $clientId		= '';

	/**
	 * @var	string	The application secret key.
	 */
	private $clientSecret	= '';

	/**
	 * @var	string	The URI to which to redirect to upon successful login.
	 */
	private $redirectUri	= '';

	/**
	 * @var string	The access token for connecting to OneDrive.
	 */
	private $accessToken	= '';

	/**
	 * @var string	The refresh token used to get a new access token for OneDrive.
	 */
	private $refreshToken	= '';

	private $lastCurlInfo;
	private $lastCurlResponse;
	private $baseOptions	= array(
		// general options
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_FOLLOWLOCATION	=> true,
		CURLOPT_AUTOREFERER		=> true,

		// SSL options
		CURLOPT_SSL_VERIFYHOST	=> false,
		CURLOPT_SSL_VERIFYPEER	=> false,
	);


	/**
	 * Constructor.
	 *
	 * @param	array	$options	The options to use while creating this object.
	 */
	public function __construct($options = array()) {
		if (array_key_exists('client_id', $options)) {
			$this->clientId	= (string) $options['client_id'];
		}
		
		if (array_key_exists('client_secret', $options)) {
			$this->clientSecret	= (string) $options['client_secret'];
		}

		if (array_key_exists('redirect_uri', $options)) {
			$this->redirectUri	= (string) $options['redirect_uri'];
		}

		if (array_key_exists('access_token', $options)) {
			$this->accessToken	= (string) $options['access_token'];
		}

		if (array_key_exists('refresh_token', $options)) {
			$this->refreshToken	= (string) $options['refresh_token'];
		}
	}

	/*
	 * =========================================================================
	 * GETTERS / SETTERS
	 * =========================================================================
	 */

	/**
	 * @return string
	 */
	public function getClientId() {
		return $this->clientId;
	}

	/**
	 * @param string $clientId
	 */
	public function setClientId($clientId) {
		$this->clientId = $clientId;
	}

	/**
	 * @return string
	 */
	public function getClientSecret() {
		return $this->clientSecret;
	}

	/**
	 * @param string $clientSecret
	 */
	public function setClientSecret($clientSecret) {
		$this->clientSecret = $clientSecret;
	}

	/**
	 * @return string
	 */
	public function getRedirectUri() {
		return $this->redirectUri;
	}

	/**
	 * @param string $redirectUri
	 */
	public function setRedirectUri($redirectUri) {
		$this->redirectUri = $redirectUri;
	}

	/**
	 * @return string
	 */
	public function getAccessToken() {
		return $this->accessToken;
	}

	/**
	 * @param string $accessToken
	 */
	public function setAccessToken($accessToken) {
		$this->accessToken = $accessToken;
	}

	/**
	 * @return string
	 */
	public function getRefreshToken() {
		return $this->refreshToken;
	}

	/**
	 * @param string $refreshToken
	 */
	public function setRefreshToken($refreshToken) {
		$this->refreshToken = $refreshToken;
	}

	/*
	 * =========================================================================
	 * AUTHENTICATION
	 * =========================================================================
	 */

	/**
	 * Construct the OAuth 2.0 authorization request URI.
	 *
	 * @param	array	$scopes			The OneDrive scopes requested by the application.
	 * @param	string	$redirectUri	The URI to which to redirect to upon successful login.
	 *
	 * @return	string
	 *
	 * @throws	\Exception	Thrown if clientId is not set.
	 */
	public function createAuthUrl($scopes, $redirectUri) {
		if ($this->clientId === null) {
			throw new \Exception('The client ID must be set to call createAuthUrl()');
		}

		$scopes			= implode(',', $scopes);
		$redirectUri	= (string) $redirectUri;
		
		$url			= self::AUTH_URL
			. '?client_id=' . urlencode($this->clientId)
			. '&scope=' . urlencode($scopes)
			. '&response_type=code'
			. '&display=popup'
			. '&redirect_uri=' . urlencode($redirectUri)
		;

		return $url;
	}

	/**
	 * Obtains a new access token from OAuth2.
	 *
	 * @param 	string $authCode	The authorization code parameter obtained from the inital callback.
	 *
	 * @return	object
	 *
	 * @throws	\Exception	Thrown if clientId is not set.
	 * @throws	\Exception	Thrown if clientSecret is not set.
	 * @throws	\Exception	Thrown if redirect URI is not set.
	 */
	public function authenticate($authCode) {
		if ($this->clientId === null) {
			throw new \Exception('The client ID must be set to call authenticate()');
		}

		if ($this->clientSecret === null) {
			throw new \Exception('The client secret must be set to call authenticate()');
		}

		if ($this->redirectUri === null) {
			throw new \Exception('The redirect URI must be set to call authenticate()');
		}

		$post	= 'client_id=' . urlencode($this->clientId)
			. '&redirect_uri=' . urlencode($this->redirectUri)
			. '&client_secret=' . urlencode($this->clientSecret)
			. '&grant_type=authorization_code'
            . '&code=' . urlencode($authCode);
		;

		$curl	= curl_init();

		curl_setopt_array($curl, array(
			// post options
			CURLOPT_URL				=> self::TOKEN_URL,
			CURLOPT_POST			=> true,
			CURLOPT_POSTFIELDS		=> $post,

			// general options
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_FOLLOWLOCATION	=> true,
			CURLOPT_AUTOREFERER		=> true,

			// SSL options
			CURLOPT_SSL_VERIFYHOST	=> false,
			CURLOPT_SSL_VERIFYPEER	=> false,
		));

		$response	= curl_exec($curl);

		if ($response === false) {
			if (curl_errno($curl)) {
				throw new \Exception('curl_exec() failed: ' . curl_error($curl));
			} else {
				throw new \Exception('curl_exec(): empty response');
			}
		}

		curl_close($curl);

		$data	= json_decode($response);

		if ($data === null) {
			throw new \Exception('json_decode() failed');
		} else if (isset($data->error)) {
			throw new \Exception($data->error_description);
		}

		$this->setAccessToken($data->access_token);
		$this->setRefreshToken($data->refresh_token);

		return $data;
	}
	
	/**
	 * Check the access token expiry.
	 */
	public function isAccessTokenExpired() {
		try {
			$this->getInformation();

			if ($this->lastHttpCode != 200) {
				return true;
			}
		} catch (\Exception $e) {
			return true;
		}

		return false;
	}

	/**
	 * Fetches a fresh OAuth 2.0 access token with the given refresh token.
	 * @param	string	$refreshToken	The refresh token obtained from a previous oAuth request.
	 *
	 * @return	object
	 *
	 * @throws	\Exception	Thrown if clientId is not set.
	 * @throws	\Exception	Thrown if clientSecret is not set.
	 * @throws	\Exception	Thrown if redirectUri is not set.
	 * @throws	\Exception	Thrown if refreshToken is not set.
	 */
	public function refreshToken($refreshToken = null) {
		if ($this->clientId === null) {
			throw new \Exception('The client ID must be set to call refreshToken()');
		}

		if ($this->clientSecret === null) {
			throw new \Exception('The client secret must be set to call refreshToken()');
		}

		if ($this->redirectUri === null) {
			throw new \Exception('The redirect URI must be set to call refreshToken()');
		}

		if ($refreshToken === null) {
			if ($this->refreshToken === null) {
				throw new \Exception('The refresh token must be set to call refreshToken()');
			} else {
				$refreshToken	= $this->refreshToken;
			}
		}

		$post	= 'client_id=' . urlencode($this->clientId)
			. '&redirect_uri=' . urlencode($this->redirectUri)
			. '&client_secret=' . urlencode($this->clientSecret)
			. '&grant_type=refresh_token'
			. '&refresh_token=' . urlencode($refreshToken)
		;

		$curl	= curl_init();

		curl_setopt_array($curl, array(
			// post options
			CURLOPT_URL				=> self::TOKEN_URL,
			CURLOPT_POST			=> true,
			CURLOPT_POSTFIELDS		=> $post,

			// general options
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_FOLLOWLOCATION	=> true,
			CURLOPT_AUTOREFERER		=> true,

			// SSL options
			CURLOPT_SSL_VERIFYHOST	=> false,
			CURLOPT_SSL_VERIFYPEER	=> false,
		));

		$response	= curl_exec($curl);

		if ($response === false) {
			if (curl_errno($curl)) {
				throw new \Exception('curl_exec() failed: ' . curl_error($curl));
			} else {
				throw new \Exception('curl_exec(): empty response');
			}
		}

		curl_close($curl);

		$data	= json_decode($response);

		if ($data === null) {
			throw new \Exception('json_decode() failed');
		} else if (isset($data->error)) {
			throw new \Exception($data->error_description);
		}

		$this->setAccessToken($data->access_token);
		$this->setRefreshToken($data->refresh_token);

		return $data;
	}

	/*
	 * =========================================================================
	 * PUBLIC API
	 * =========================================================================
	 */

	/**
	 * Processes a result returned by the OneDrive API call using cURL object.
	 *
	 * @param	string	$method			The HTTP method.
	 * @param	string	$relativeUrl	The relative URL to call.
	 * @param	array	$options		Extra options to apply.
	 *
	 * @return	object|string			The content returnd, as an object instance if served a JSON, or as a string if served as anything else.
	 * @throws \Exception		Thrown if curl_exec() fails.
	 */
	public function fetch($method, $relativeUrl, $options = array()) {
		$method	= strtoupper($method);

		$url	= $relativeUrl;

		if (substr($url, 0, 6) != 'https:') {
			$url	= self::API_URL . ltrim($url, '/');
		}

		// expect status
		$expectHttpStatus	= false;

		if (isset($options['expect-status'])) {
			$expectHttpStatus	= $options['expect-status'];
		}

		// merge curl options
		$curlOptions	= $this->baseOptions;

		if (isset($options['curl-options']) && is_array($options['curl-options'])) {
			foreach ($options['curl-options'] as $k => $v) {
				$curlOptions[$k]	= $v;
			}
		}

		// follow redirect
		if (isset($options['follow-redirect']) && !$options['follow-redirect']) {
			$curlOptions[CURLOPT_FOLLOWLOCATION]	= false;
		}

		// headers
		$headers		= array();

		if (isset($options['headers'])) {
			$headers	= $options['headers'];
		}

		if ($method == 'POST') {
			$headers[]	= 'Content-Type: application/json';
		}

		$headers[]	= 'Authorization: bearer ' . $this->accessToken;

		$curlOptions[CURLOPT_HTTPHEADER]	= $headers;

		// handle files
		$file			= null;
		$fp				= null;

		if (isset($options['file'])) {
			$file	= $options['file'];
		}

		if (!isset($options['fp']) && !empty($file)) {
			$fp	= @fopen($file, $method == 'GET' ? 'wb' : 'rb');

			if ($fp === false) {
				throw new \Exception('Could not open ' . $file . ' for ' . ($method == 'GET' ? 'writing' : 'reading'));
			}
		} else if (isset($options['fp'])) {
			$fp	= $options['fp'];
		}

		// post data
		$postData		= false;

		if (isset($options['post-data'])) {
			$postData	= $options['post-data'];

			if (is_array($postData) || is_object($postData)) {
				$postData	= json_encode((object) $postData);
			}
		}

		//
		if ($method == 'GET' && $fp) {
			$curlOptions[CURLOPT_RETURNTRANSFER]	= false;
			$curlOptions[CURLOPT_HEADER]			= false;
			$curlOptions[CURLOPT_FILE]				= $fp;
			$curlOptions[CURLOPT_BINARYTRANSFER]	= true;

			if (!$expectHttpStatus) {
				$expectHttpStatus	= 200;
			}
		} else if ($method == 'POST') {
			$curlOptions[CURLOPT_POST]	= true;

			if ($postData) {
				$curlOptions[CURLOPT_POSTFIELDS]	= $postData;
			}
		} else if ($method == 'PUT' && $fp) {
			$stat	= fstat($fp);
			$size	= @$stat['size'];

			$curlOptions[CURLOPT_PUT]			= true;
			$curlOptions[CURLOPT_INFILE]		= $fp;
			$curlOptions[CURLOPT_INFILESIZE]	= $size;
		} else {
			$curlOptions[CURLOPT_CUSTOMREQUEST]	= $method;

			if ($postData) {
				$curlOptions[CURLOPT_POSTFIELDS]	= $postData;
			}
		}

		$curl			= curl_init($url);

		curl_setopt_array($curl, $curlOptions);

		$response		= curl_exec($curl);
		$errNo			= curl_errno($curl);
		$error			= curl_error($curl);
		$info			= curl_getinfo($curl);

		$this->lastCurlInfo		= $info;
		$this->lastCurlResponse	= $response;

		curl_close($curl);

		if ($fp) {
			@fclose($fp);
		}
		
		if ($errNo) {
			throw new \Exception('curl_exec() failed: ' . $error, $errNo);
		}

		$lastHttpCode	= array_key_exists('http_code', $info) ? $info['http_code'] : null;

		if ($expectHttpStatus && $expectHttpStatus != $lastHttpCode) {
			throw new \Exception('Unexpected HTTP status code ' . $lastHttpCode, $lastHttpCode);
		}

		$contentType	= array_key_exists('content_type', $info) ? $info['content_type'] : null;

		if (!preg_match('|^application/json|', $contentType)) {
			return $response;
		}

		if ($response == '') {
			return (object) array();
		}

		$data	= json_decode($response);

		if (!$data) {
			throw new \Exception('Invalid JSON data received');
		}

		if (isset($data->error)) {
			throw new \Exception($data->error->message, (int) $data->error->code);
		}

		return $data;
	}

	///////////////////////////////////////////////////////

	/**
	 * Normalize the path of a resource inside OneDrive.
	 *
	 * @param	string	$relativePath	The relative path to OneDrive's root.
	 * @param	string	$collection		The collection of the path you want to access or an action.
	 *
	 * @return	string
	 */
	protected function normalizePath($relativePath, $collection = '') {
		$relativePath	= trim($relativePath, '/');

		if (empty($relativePath)) {
			$path	= 'drive/root';

			if ($collection) {
				$path	.= '/' . $collection;
			}

			return $path;
		}

		$path	= 'drive/root:/' . $relativePath;

		if ($collection) {
			$path	.= ':/' . $collection;
		}

		$path	= str_replace(' ', '%20', $path);

		return $path;
	}

	/**
	 * Returns information about the default OneDrive in the account.
	 *
	 * @return	object|string	See http://onedrive.github.io/resources/drive.htm
	 */
	public function getInformation() {
		return $this->fetch('get', 'drive');
	}

	/**
	 * Get the raw listing of a folder.
	 *
	 * @param	string	$path	The relative path of the folder to list its contents.
	 * @param	string	$search	If set returns only items matching the search criteria.
	 *
	 * @return	object|string	See http://onedrive.github.io/items/list.htm
	 */
	public function getRawContents($path, $search = null) {
		$relativeUrl	= $this->normalizePath($path, 'children');

		if ($search) {
			$relativeUrl	= $this->normalizePath($path, 'view.search');
		}

		$relativeUrl	.= '?orderby=name%20asc';

		if ($search) {
			$relativeUrl	.= '&q=' . urlencode($search);
		}

		$response = $this->fetch('get', $relativeUrl);

		return $response;
	}

	/**
	 * Get the processed listing of a folder.
	 *
	 * @param	string	$path	The relative path of the folder to list its contents.
	 * @param	string	$search	If set returns only items matching the search criteria.
	 *
	 * @return	array	Two arrays under keys folders and files. Each array's key is the file/folder name, the value is number of children (folder) or size in bytes (file).
	 */
	public function listContents($path = '/', $search = null) {
		$result	= $this->getRawContents($path, $search);

		$return	= array(
			'files'		=> array(),
			'folders'	=> array(),
		);

		if (!isset($result->value) || !count($result->value)) {
			return $return;
		}

		foreach ($result->value as $item) {
			if (isset($item->folder)) {
				$return['folders'][$item->name]	= $item->folder->childCount;

				continue;
			}

			$return['files'][$item->name]	= $item->size;
		}

		return $return;
	}

	/**
	 * Uploads a file of up to 100MB in size.
	 *
	 * @param	string	$path	The remote path relative to OneDrive root.
	 * @param	string	$file	The absolute local filesystem path.
	 *
	 * @return	object|string	See http://onedrive.github.io/items/upload_put.htm
	 * @throws	\Exception		Thrown if file size greater than 100MB.
	 */
	public function simpleUpload($path, $file) {
		clearstatcache();

		$size			= @filesize($file);

		if ($size > 104857600) {
			throw new \Exception('File size is too big for simpleUpload.');
		}

		$relativeUrl	= $this->normalizePath($path, 'content') . '?' . urlencode('@name.conflictBehavior') . '=replace';

		$options		= array(
			'headers'		=> array('Content-Type: application/octet-stream'),
			'file'			=> $file,
		);

		$response		= $this->fetch('put', $relativeUrl, $options);

		return $response;
	}

	/**
	 * Creates a new multipart upload session and returns its upload URL.
	 *
	 * @param	string	$path	Relative path in the OneDrive.
	 *
	 * @return	string	The upload URL for the session. See https://dev.onedrive.com/items/upload_large_files.htm#create-an-upload-session
	 */
	public function createUploadSession($path) {
		$relativeUrl	= $this->normalizePath($path, 'upload.createSession');
		$postData		= (object) array(
			'item'	=> array(
				'@name.conflictBehavior'	=> 'replace',
				'name'						=> basename($path),
			)
		);

		$options	= array(
			'post-data'		=> $postData,
		);

		$response	= $this->fetch('post', $relativeUrl, $options);

		return $response->uploadUrl;
	}

	/**
	 * Destroy an already started upload session.
	 *
	 * @param	string	$url	The URL of the upload session.
	 *
	 * @return	object|string	See https://dev.onedrive.com/items/upload_large_files.htm#cancel-the-upload-session
	 *
	 * @throws 	\Exception		Thrown if status code is not 204.
	 */
	public function cancelUploadSession($url) {
		$options	= array(
			'expect-code'	=> 204,
		);

		$response	= $this->fetch('delete', $url, $options);

		return $response;
	}

	/**
	 * Upload a file fragment.
	 *
	 * @param	string	$sessionUrl	The upload session URL, see createUploadSession.
	 * @para	string	$file		Absolute filesystem path of the source file.
	 * @param	int		$from		Starting byte to begin uploading, default is 0 (start of file).
	 * @param	int		$length		Chunk size in bytes, the default is 10MB, must NOT be over 60MB, must be a multiple of 320KB.
	 * @return	object|string		The upload information, see http://onedrive.github.io/items/upload_large_files.htm
	 * @throws	\Exception
	 */
	public function uploadFragment($sessionUrl, $file, $from = 0, $length = 10485760) {
		clearstatcache();

		$size	= filesize($file);
		$to		= $from + $length - 1;

		if ($to > ($size - 1)) {
			$to	= $size - 1;
		}

		$contentLength	= $to - $from + 1;
		$range			= $from . '-' . $to . '/' . $size;
		$fp				= @fopen($file, 'rb');

		if ($fp === false) {
			throw new \Exception('Could not open ' . $file . ' for reading');
		}

		fseek($fp, $from);
		$data			= fread($fp, $contentLength);

		$options	= array(
			'headers'	=> array(
				'Content-Length: ' . $contentLength,
				'Content-Range: bytes ' . $range
			),
			'post-data'	=> $data
		);

		fclose($fp);

		return $this->fetch('put', $sessionUrl, $options);
	}

	/**
	 * Upload a file using multipart uploads. Useful for files over 100MB and up to 2GB.
	 *
	 * @param	string	$path		Relative path in the OneDrive.
	 * @para	string	$file		Absolute filesystem path of the source file.
	 * @param	int		$partSize	Fragment size in bytes, the default is 10MB, must NOT be over 60MB, must be a multiple of 320KB.
	 * @return	object|string		The upload information, see http://onedrive.github.io/items/upload_large_files.htm
	 * @throws	\Exception	Thrown if upload failed.
	 */
	public function resumableUpload($path, $file, $partSize = 10485760) {
		$sessionUrl	= $this->createUploadSession($path);
		$from		= 0;

		while (true) {
			try {
				$result	= $this->uploadFragment($sessionUrl, $file, $from, $partSize);
			} catch (\Exception $e) {
				try {
					$this->cancelUploadSession($sessionUrl);
				} catch (\Exception $ex) {

				}

				throw $e;
			}

			$from	+= $partSize;

			if (isset($result->name)) {
				return $result;
			}
		}

		return false;
	}

	/**
	 * Automatically decides which upload method to use to upload a file to OneDrive.
	 *
	 * @param	string	$path		Relative path in the OneDrive.
	 * @para	string	$file		Absolute filesystem path of the source file.
	 *
	 * @return	object|string		The upload information, see http://onedrive.github.io/items/upload_put.htm
	 */
	public function upload($path, $file) {
		clearstatcache();
		$size	= @filesize($file);

		if ($size > 104857600) {
			return $this->resumableUpload($path, $file);
		} else {
			return $this->simpleUpload($path, $file);
		}
	}

	/**
	 * Download a file.
	 *
	 * @param	string	$path	The path of the file in OneDrive.
	 * @param	string	$file	The absolute filesystem path where the file will be downloaded to.
	 *
	 * @return	object|string	See https://dev.onedrive.com/items/download.htm
	 *
	 * @throws	\Exception
	 */
	public function download($path, $file) {
		$relativeUrl	= $this->normalizePath($path, 'content');
		$options		= array(
			'file'			=> $file,
			'curl-options'	=> array(
				CURLOPT_HEADER			=> false,
			),
		);

		return $this->fetch('get', $relativeUrl, $options);
	}

	/**
	 * Get a signed download URL for the remote file with the specified relative path to OneDrive's root.
	 *
	 * @param	string	$apth	The path of the file in OneDrive.
	 *
	 * @return	string|bool		Signed URL to download the file's contents. False if error.
	 */
	public function getDownloadUrl($path) {
		$relativeUrl	= $this->normalizePath($path, 'content');
		$options		= array(
			'curl-options'	=> array(
				CURLOPT_HEADER	=> true,
			),
			'follow-redirect'	=> false,
		);

		$response		= $this->fetch('get', $relativeUrl, $options);

		$response		= str_replace("\r\n", "\n", $response);
		$lines			= explode("\n", $response);

		foreach ($lines as $line) {
			if (strpos($line, 'Location: ') === 0) {
				list(, $location)	= explode(': ', $line, 2);

				return $location;
			}
		}

		throw new \Exception('Could not get the download URL');
	}

	/**
	 * Create a folder including all of its parent folders.
	 *
	 * @param	string	$path	The path to create.
	 * @throws	\Exception
	 */
	public function createFolder($path) {
		$path	= trim($path, '/');

		if (empty($path)) {
			return;
		}

		// get the parent path and folder components of the path
		$parentPath	= '/';
		$folder		= $path;

		if (strpos($path, '/') !== false) {
			$pathParts	= explode('/', $path);
			$folder		= array_pop($pathParts);
			$parentPath	= implode('/', $pathParts);
		}

		try {
			$this->listContents($parentPath, $folder);
		} catch (\Exception $e) {
			$this->createFolder($parentPath);
		}

		// we have to create a new folder in parent folder.
		$relativeUrl	= $this->normalizePath($parentPath, 'children');
		$postData		= (object) array(
			'name'			=> $folder,
			'folder'		=> (object) array()
		);

		$options	= array(
			'post-data'	=> $postData,
		);

		try {
			return $this->fetch('post', $relativeUrl, $options);
		} catch (\Exception $e) {
			if (stripos($e->getMessage(), 'nameAlreadyExists') === false) {
				throw $e;
			}
		}
	}
}