<?php

namespace WSColissimo\WSColiPosteLetterService;

use WSColissimo\WSColiPosteLetterService\Request\LetterColissimoRequest;

/**
 * A client for the WSColiPosteLetterService
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class Client implements ClientInterface
{
    /**
     * PHP SOAP client for interacting with the WSColiPosteLetterService API
     *
     * @var SoapClient
     */
    protected $soapClient;

    /**
     * Construct SO Flexibilite SOAP client
     *
     * @param \SoapClient $soapClient
     */
    public function __construct(\SoapClient $soapClient)
    {
        $this->soapClient = $soapClient;
    }

    /**
     * (non-PHPdoc)
     * @see \WSColissimo\WSColiPosteLetterService\ClientInterface::getLetterColissimo()
     */
    public function getLetterColissimo(LetterColissimoRequest $request)
    {
        return $this->soapClient->__soapCall('getLetterColissimo', array($request));
    }

    /**
     * @param $request
     * @return mixed
     */
    public function generateLabel($request)
    {
        return $this->soapClient->__soapCall('generateLabel', $request );
    }


    public function getSoapClientLastRequest()
	{
		return $this->soapClient->__getLastRequest();
	}

    public function getAllFunction()
    {
        return $this->soapClient->__getFunctions();
    }

    public function getLastRequest()
    {
        return $this->soapClient->__getLastRequest();
    }

    public function getLastResponse()
    {
        return $this->soapClient->__getLastResponse();
    }
}
