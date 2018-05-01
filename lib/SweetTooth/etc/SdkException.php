<?php



/**
 * Thrown when the Sweet Tooth SDK isn't able to proceed as far as hitting the API
 */
class SweetToothSdkException extends Exception
{
    const CREDENTIALS_NOT_SPECIFIED = 100;

    /**
     * To make debugging easier.
     *
     * @return string The string representation of the error
     */
    public function __toString()
    {
        $str = 'Exception: ';
        if ($this->code != 0) {
            $str .= $this->code . ': ';
        }

        //Prevents the SDK from returning no response when the call doesn't make it to platform
        if (strlen($this->message) == 0){
            $str .= "Unable to send to platform due to incorrect SDK usage.";
        }

        return $str . $this->message;
    }
}
