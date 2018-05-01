<?php

namespace WSColissimo\WSColiPosteLetterService\Request\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Parcel
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class Parcel
{
    /**
     * @var float
     */
    public $weight;

    /**
     * @var boolean
     */
    //public $pickupLocationId = '0' ;


    /**
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param string $insuranceRange
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function getPickupLocationId()
    {
        return $this->pickupLocationId;
    }

    /**
     * @param string $pickupLocationId
     */
    public function setPickupLocationId($pickupLocationId)
    {
        if($pickupLocationId!='')
            $this->pickupLocationId = $pickupLocationId;
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
