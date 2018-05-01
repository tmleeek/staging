<?php

namespace WSColissimo\WSColiPosteLetterService\Request\ValueObject;

/**
 * Letter
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class Letter
{
    /**
     * @var string
     */
    public $contractNumber;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    //public $outputFormat = array('outputPrintingType' => 'PDF_A4_300dpi');
    public $outputFormat = array('outputPrintingType' => 'PDF_10x15_300dpi');

    /**
     * @var
     */
    public $letter;

    /**
     * @return integer
     */
    public function getContractNumber()
    {
        return $this->contractNumber;
    }

    /**
     * @param integer $contractNumber
     */
    public function setContractNumber($contractNumber)
    {
        $this->contractNumber = (int) $contractNumber;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getOutputFormat($outputFormat)
    {
        return $this->outputFormat;
    }

    /**
     * @param string $outputFormat
     */
    public function setOutputFormat($outputFormat)
    {
        $this->outputFormat['outputPrintingType'] = $outputFormat;
    }

    /**
     * @return mixed
     */
    public function getLetter()
    {
        return $this->letter;
    }

    /**
     * @param $letter
     */
    public function setLetter($letter)
    {
        $this->letter = $letter;
    }

    /**
     * Getter
     *
     * @param string $name
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

}

class LetterSUB
{
    /**
     * @var ServiceCallContext
     */
    public $service;

    /**
     * @var Parcel
     */
    public $parcel;

    /**
     * @var ExpEnv
     */
    public $sender;

    /**
     * @var DestEnv
     */
    public $addressee;

    /**
     * @return ServiceCallContext
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param ServiceCallContext $service
     */
    public function setService(ServiceCallContext $service)
    {
        $this->service = $service;
    }

    /**
     * @return Parcel
     */
    public function getParcel()
    {
        return $this->parcel;
    }

    /**
     * @param Parcel $parcel
     */
    public function setParcel(Parcel $parcel)
    {
        $this->parcel = $parcel;
    }

    /**
     * @return ExpEnv
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param ExpEnv $exp
     */
    public function setSender(ExpEnv $sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return mixed
     */
    public function getAddressee()
    {
        return $this->addressee;
    }

    /**
     * @param DestEnv $addresse
     */
    public function setAddressee(DestEnv $addresse)
    {
        $this->addressee = $addresse;
    }

    /**
     * Getter
     *
     * @param string $name
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

}
