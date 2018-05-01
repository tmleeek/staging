<?php

namespace WSColissimo\WSColiPosteLetterService\Request\ValueObject;


/**
 * ServiceCallContext
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class ServiceCallContext
{
    /**
     * @var string
     */
    public $productCode;

    /**
     * @var \DateTime
     */
    public $depositDate ;

    /**
     * @var string
     */
    public $orderNumber;

    /**
     * @var string
     */
    public $commercialName;

    /**
     * @var string
     */
    public $returnTypeChoice ;


    /**
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * @param string $productCode
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;
    }

    /**
     * @return \DateTime
     */
    public function getDepositDate()
    {
        return $this->depositDate;
    }

    /**
     * @param null $depositDate
     */
    public function setDepositDate($depositDate = NULL)
    {
        $this->depositDate = $depositDate;
        if(!$depositDate){
            $this->depositDate = date('Y-m-d');
        }
    }

    /**
     * @param string $orderNumber
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @return string
     */
    public function getCommercialName()
    {
        return $this->commercialName;
    }

    /**
     * @param string $commercialName
     */
    public function setCommercialName($commercialName)
    {
        $this->commercialName = $commercialName;
    }

    /**
     * @return string
     */
    public function getReturnTypeChoice()
    {
        return $this->returnTypeChoice;
    }

    /**
     * @param string $returnTypeChoice
     */
    public function setReturnTypeChoice($returnTypeChoice)
    {
        $this->returnTypeChoice = $returnTypeChoice;
    }
    /**
     * @param string $name
     *
     * @return boolean
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

}
