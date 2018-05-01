<?php
namespace Mpm\GatewayClient;


class CurlClient
{
    public $_logFilePath = null;

    public function get($url, $params)
    {

        if (isset($params['body']))
        {
            if (!$this->endsWith($url, '?'))
                $url .= '&';
            $url .= http_build_query($params['body']);
        }
        $url = str_replace('gateway.mpm.com', '178.170.72.26', $url);

        $headers = $this->buildHeaders($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);

        $result = curl_exec ($ch);
        curl_close($ch);

        $this->log("get | ".$url);

        return $result;

    }

    /**
     * Process a POST request
     * @param $url
     * @param $params
     * @return mixed
     */
    public function post($url, $params, $attachments = null)
    {
        $url = str_replace('gateway.mpm.com', '178.170.72.26', $url);

        $headers = $this->buildHeaders($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (!$attachments){
            $postData = isset($params['body']) ? http_build_query($params['body']) : '';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_POST, count($params['body']));
        } else {
            if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                $filename = str_replace('@', '', $attachments['catalog']);
                $file = array('catalog' => new \CURLFile($filename));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
            } else {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $attachments);
            }
        }

        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);

        $result = curl_exec ($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->log("post | ".$url);

        switch($httpStatus)
        {
            case 401:
                throw new \Exception('Not authorized');
                break;
        }


        return $result;
    }

    public function put($url, $params)
    {
        $url = str_replace('gateway.mpm.com', '178.170.72.26', $url);

        $headers = $this->buildHeaders($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $postData = isset($params['body']) ? http_build_query($params['body']) : '';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);

        $result = curl_exec ($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->log("put | ".$url);

        switch($httpStatus) {
            case 401:
                throw new \Exception('Not authorized');
                break;
        }


        return $result;
    }

    public function delete($url, $params, $attachments = null)
    {
        $url = str_replace('gateway.mpm.com', '178.170.72.26', $url);

        $headers = $this->buildHeaders($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);

        $result = curl_exec ($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->log("delete | ".$url);

        switch($httpStatus) {
            case 401:
                throw new \Exception('Not authorized');
                break;
        }

        return $result;
    }

    /**
     * @param $params
     * @return array
     */
    protected function buildHeaders($params)
    {
        $headers = array();
        $headers[] = 'Host: gateway.mpm.com';
        if (isset($params['headers']))
        {
            foreach($params['headers'] as $k => $v)
            {
                $headers[] = $k.': '.$v;
            }
        }
        return $headers;
    }

    protected function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    protected function log($msg)
    {
        if ($this->_logFilePath)
        {
            $msg = date("Y-m-d H:i:s")." : ".$msg."\n";
            @file_put_contents($this->_logFilePath, $msg, FILE_APPEND);
        }
    }

}