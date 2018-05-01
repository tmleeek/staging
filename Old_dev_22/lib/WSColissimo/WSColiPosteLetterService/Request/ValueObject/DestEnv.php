<?php

namespace WSColissimo\WSColiPosteLetterService\Request\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * DestEnvVO
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class DestEnv
{
    /**
     * @var Address
     */
    public $address;


    /**
     * Constructor
     *
     * @param Address $address
     */
    public function __construct(Address $address = null)
    {   
        $this->address = $address;
    }


    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
