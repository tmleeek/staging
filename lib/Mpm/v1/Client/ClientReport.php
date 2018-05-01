<?php
namespace Mpm\GatewayClient\Client;

use GuzzleHttp\Post\PostFile;
use Mpm\GatewayClient\Client,
    Mpm\GatewayClient\Exceptions\FileNotFoundException;

class ClientReport extends Client
{
    public static function getInstance()
    {
        if (self::$_instance === null || false === (self::$_instance instanceof Catalog)) {
            self::$_instance = new ClientReport();
        }
        return self::$_instance;
    }

    /**
     * Request a new report and return report ID
     *
     * @param $source
     * @param string $format
     */
    public function request($source, $params = array(), $format = 'csv')
    {
        $params['source'] = $source;
        $params['report_format'] = $format;

        $url = '/client-report';

        $result = $this->post($url, $params);
        $result = json_decode($result);


        return $result->body->report_id;
    }

    /**
     *
     */
    public function getReportList()
    {
        $params = array('sortBy' => 'id,DESC');
        $url = '/client-report';

        $result = $this->get($url, $params);
        $result = json_decode($result);

        return $result->body->results;
    }

    public function getReportContent($reportId)
    {
        $url = '/client-report/'.$reportId;

        $result = $this->get($url);

        $result = json_decode($result);

        return base64_decode($result->body->content);
    }

}