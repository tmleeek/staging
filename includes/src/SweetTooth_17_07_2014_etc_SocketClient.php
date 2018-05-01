<?php

class SweetToothSocketClient
{
    // TODO: This variable should be curl-agnostic.  Leaving for now for consistent interface between this and Pest.
    public $curlOpts = array(
        CURLOPT_TIMEOUT_MS => 10000,
        CURLOPT_FOLLOWLOCATION => false
    );

    public $baseUrl;

    public $lastResponse;
    public $lastRequest;

    public $throwExceptions = false;

    public function __construct($baseUrl)
    {
        if (!function_exists('fsockopen')) {
            throw new Exception('Sweet Tooth requires the fsockopen() function, but it is not available.');
        }

        $this->baseUrl = $baseUrl;
    }

    /**
     * Sets up authentication on the request
     * @param string $user Username for authentication
     * @param string $pass Password for authentication
     * @param string $auth Currently only 'basic' is supported
     * @return self
     */
    public function setupAuth($user, $pass, $auth = 'basic')
    {
        $this->curlOpts[CURLOPT_HTTPAUTH] = constant('CURLAUTH_'.strtoupper($auth));
        $this->curlOpts[CURLOPT_USERPWD] = $user . ":" . $pass;
        
        return $this;
    }

    /**
     * Executes a GET request.
     * @param string $url The URL or resource to which to send the request.
     * @return string The response body.
     */
    public function get($url)
    {
        throw new Exception("This REST Client currently only supports POST requests, but you are trying to use it for GET.");

        $curl = $this->_prepRequest($this->curlOpts, $url);
        $body = $this->_doRequest($curl);
        $body = $this->_processBody($body);

        return $body;
    }

    /**
     * Executes a POST request.
     * 
     * @param string $url The URL or resource to which to send the request.
     * @param array $data An array of data to pass to the resource as POST parameters.
     * @param array $headers Optional headers to add to the request.
     * 
     * @return string The response body.
     */
    public function post($url, $data, $headers=array())
    {
        $data = json_encode($data);
        $data = (is_array($data)) ? http_build_query($data) : $data;

        $curlOpts = $this->curlOpts;
        $curlOpts[CURLOPT_CUSTOMREQUEST] = 'POST';
        $headers[] = 'Content-Length: ' . strlen($data);
        $curlOpts[CURLOPT_HTTPHEADER] = $headers;
        $curlOpts[CURLOPT_POSTFIELDS] = $data;

        list($socket, $postData) = $this->_prepRequest($curlOpts, $url);
        $body = $this->_doRequest($socket, $postData);
        //$body = $this->_processBody($body);

        return $body;
    }

    /**
     * Executes a PUT request.
     * 
     * @param string $url The URL or resource to which to send the request.
     * @param array $data An array of data to pass to the resource as PUT parameters.
     * @param array $headers Optional headers to add to the request.
     * 
     * @return string The response body.
     */
    public function put($url, $data, $headers=array())
    {
        throw new Exception("This REST Client currently only supports POST requests, but you are trying to use it for PUT.");

        $data = json_encode($data);
        $data = (is_array($data)) ? http_build_query($data) : $data;

        $curlOpts = $this->curlOpts;
        $curlOpts[CURLOPT_CUSTOMREQUEST] = 'PUT';
        $headers[] = 'Content-Length: ' . strlen($data);
        $curlOpts[CURLOPT_HTTPHEADER] = $headers;
        $curlOpts[CURLOPT_POSTFIELDS] = $data;

        $curl = $this->_prepRequest($curlOpts, $url);
        $body = $this->_doRequest($curl);
        $body = $this->_processBody($body);

        return $body;
    }

    /**
     * Executes a DELETE request.
     * @param string $url The URL or resource to which to send the request.
     * @return string The response body.
     */
    public function delete($url)
    {
        throw new Exception("This REST Client currently only supports POST requests, but you are trying to use it for DELETE.");

        $curlOpts = $this->curlOpts;
        $curlOpts[CURLOPT_CUSTOMREQUEST] = 'DELETE';

        $curl = $this->_prepRequest($curlOpts, $url);
        $body = $this->_doRequest($curl);
        $body = $this->_processBody($body);

        return $body;
    }

    /**
     * @return string
     */
    public function lastBody()
    {
        return $this->lastResponse['body'];
    }

    /**
     * @return string
     */
    public function lastStatus()
    {
        return $this->lastResponse['meta']['http_code'];
    }

    /**
     * Post-processing on the response body before sending it back to the user.
     * @param string $body
     * @return string
     */
    protected function _processBody($body)
    {
        // Override this in classes that extend Pest.
        // The body of every GET/POST/PUT/DELETE response goes through
        // here prior to being returned.
        return json_decode($body, true);
    }

    /**
     * Post-processing on the error response before sending it back to the user.
     * @param string $body
     * @return string
     */
    protected function _processError($body)
    {
        // Override this in classes that extend Pest.
        // The body of every erroneous (non-2xx/3xx) GET/POST/PUT/DELETE
        // response goes through here prior to being used as the 'message'
        // of the resulting Pest_Exception
        return $body;
    }

    /**
     * Prepares everything to make the request
     * @param array $opts Settings for the request, in the form of curl options
     * @param string $url
     * @throws Exception
     * @return mixed array(socket pointer, string data to write to socket)
     */
    protected function _prepRequest($opts, $url)
    {
        $opts[CURLOPT_HTTPHEADER][] = 'Accept: application/json';
        $opts[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';

        if (strncmp($url, $this->baseUrl, strlen($this->baseUrl)) != 0) {
            $url = $this->baseUrl . $url;
        }

        $urlParts = parse_url($url);

        $socket = fsockopen(
            $urlParts['host'],
            isset($urlParts['port']) ? $urlParts['port'] : 80,
            $errno,
            $errstr,
            10  // Timeout in seconds
        );

        if (!$socket) {
            throw new Exception("ST: Couldn't open a socket to {$resource} ({$errno}: {$errstr})");
        }

        // prepare method and auth settings, if available
        $method = isset($opts[CURLOPT_CUSTOMREQUEST]) ? $opts[CURLOPT_CUSTOMREQUEST] : 'GET';
        $auth = null;
        if (isset($opts[CURLOPT_HTTPAUTH])) {
            switch ($opts[CURLOPT_HTTPAUTH]) {
                case CURLAUTH_BASIC:
                    $auth = "Basic " . base64_encode($opts[CURLOPT_USERPWD]);
                    break;
            }
        }

        // setup all the HTTP lines to make up the request
        $httpLines = array();
        $httpLines[] = "{$method} {$urlParts['path']} HTTP/1.1";
        $httpLines[] = "Host: {$urlParts['host']}";
        if ($auth) {
            $httpLines[] = "Authorization: {$auth}";
        }
        if (isset($opts[CURLOPT_HTTPHEADER])) {
            $httpLines = array_merge($httpLines, $opts[CURLOPT_HTTPHEADER]);
        }
        $httpLines[] = "Connection: Close";

        // concat all the HTTP lines with Win newlines, as per the protocol, and tack on the post data
        $postData  = implode("\r\n", $httpLines);
        $postData .= "\r\n\r\n" . $opts[CURLOPT_POSTFIELDS];

        $this->lastRequest = array(
            'url' => $url
        );

        if (isset($opts[CURLOPT_CUSTOMREQUEST])) {
            $this->lastRequest['method'] = $opts[CURLOPT_CUSTOMREQUEST];
        } else {
            $this->lastRequest['method'] = 'GET';
        }

        if (isset($opts[CURLOPT_POSTFIELDS])) {
            $this->lastRequest['data'] = $opts[CURLOPT_POSTFIELDS];
        }

        return array($socket, $postData);
    }

    protected function _doRequest($socket, $postData)
    {
        fwrite($socket, $postData);

        // TODO: here is where we would read from fp if we want the response
        // TODO: is there any way to get socket metadata like curl_getinfo ?
        //$this->lastResponse = array(
        //    'body' => $body,
        //    'meta' => $meta
        //);

        fclose($socket);

        $this->_checkLastResponseForError();

        //return $body;
        return "";
    }

    /**
     * 
     * @return self
     */
    protected function _checkLastResponseForError()
    {
        // TODO: currently this should always be false - we do not currently support responses
        if (!$this->throwExceptions) {
            return $this;
        }

        $meta = $this->lastResponse['meta'];
        $body = $this->lastResponse['body'];

        if (!$meta) {
            return $this;
        }

        //Was spitting out errors to PHP console
        //Latest version of PEST had this removed from method, commenting for time being
        // error_log("Pest error.  Meta: " . print_r($meta,1)
        //     . "\r\nLast Request: " . print_r($this->lastRequest,1)
        //     . "\r\nUser/pass: " . $this->curlOpts[CURLOPT_USERPWD]
        // );

        $err = null;
        switch ($meta['http_code']) {
            case 400:
                throw new Pest_BadRequest($this->_processError($body));
                break;
            case 401:
                throw new Pest_Unauthorized($this->_processError($body));
                break;
            case 403:
                throw new Pest_Forbidden($this->_processError($body));
                break;
            case 404:
                throw new Pest_NotFound($this->_processError($body));
                break;
            case 405:
                throw new Pest_MethodNotAllowed($this->_processError($body));
                break;
            case 409:
                throw new Pest_Conflict($this->_processError($body));
                break;
            case 410:
                throw new Pest_Gone($this->_processError($body));
                break;
            case 422:
                // Unprocessable Entity -- see http://www.iana.org/assignments/http-status-codes
                // This is now commonly used (in Rails, at least) to indicate
                // a response to a request that is syntactically correct,
                // but semantically invalid (for example, when trying to
                // create a resource with some required fields missing)
                throw new Pest_InvalidRecord($this->_processError($body));
                break;
            default:
                if ($meta['http_code'] >= 400 && $meta['http_code'] <= 499) {
                    throw new Pest_ClientError($this->_processError($body));
                } else if ($meta['http_code'] >= 500 && $meta['http_code'] <= 599) {
                    throw new Pest_ServerError($this->_processError($body));
                } else if (!$meta['http_code'] || $meta['http_code'] >= 600) {
                    throw new Pest_UnknownResponse($this->_processError($body));
                }
        }
        
        return $this;
    }
}
