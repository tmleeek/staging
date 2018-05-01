<?php

namespace WSColissimo\WSColiPosteLetterService\Request\ValueObject;

/**
 * Address
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class Address
{
    /**
     * @var string
     */
    public $companyName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $line0;

    /**
     * @var string
     */
    public $line1;

    /**
     * @var string
     */
    public $line2;

    /**
     * @var string
     */
    public $countryCode;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $zipCode;

    /**
     * @var string
     */

    public $phoneNumber;

    public $mobileNumber;

    public $doorCode1;

    /**
     * @var string
     */
    public $email;

    //public $phoneNumber;
    public $intercom;

    public $language;


    //public $senderParcelRef;
    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLine0()
    {
        return $this->line0;
    }

    /**
     * @param string $line0
     */
    public function setLine0($line0)
    {
        $this->line0 = $line0;
    }

    /**
     * @return string
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * @param string $line1
     */
    public function setLine1($line1)
    {
        $this->line1 = $line1;
    }

    /**
     * @return string
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * @param string $line2
     */
    public function setLine2($line2)
    {
        $this->line2 = $line2;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param string $postalCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }
    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $mobileNumber
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }
    /**
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    public function getDoorCode1()
    {
        return $this->doorCode1;
    }

    public function setDoorCode1($doorCode1)
    {
        $this->doorCode1 = $doorCode1;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function setIntercom($intercom)
    {
        $this->intercom = $intercom;
    }

	public function getIntercom()
    {
        return $this->intercom;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

	public function getLanguage()
    {
        return $this->language;
    }

	// Customize code added.
    /*
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }*/


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
