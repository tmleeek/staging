<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Connector_Connection_Response_Message_Set
{
    /** @var Ess_M2ePro_Model_Connector_Connection_Response_Message[] $entities */
    private $entities = array();

    //########################################

    public function init(array $responseData)
    {
        foreach ($responseData as $messageData) {
            $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
            $message->initFromResponseData($messageData);

            $this->entities[] = $message;
        }
    }

    //########################################

    public function getEntities()
    {
        return $this->entities;
    }

    public function getEntitiesAsArrays()
    {
        $result = array();

        foreach ($this->getEntities() as $message) {
            $result[] = $message->asArray();
        }

        return $result;
    }

    //########################################

    /**
     * @return Ess_M2ePro_Model_Connector_Connection_Response_Message[]
     */
    public function getErrorEntities()
    {
        $messages = array();

        foreach ($this->getEntities() as $message) {
            $message->isError() && $messages[] = $message;
        }

        return $messages;
    }

    /**
     * @return Ess_M2ePro_Model_Connector_Connection_Response_Message[]
     */
    public function getWarningEntities()
    {
        $messages = array();

        foreach ($this->getEntities() as $message) {
            $message->isWarning() && $messages[] = $message;
        }

        return $messages;
    }

    /**
     * @return Ess_M2ePro_Model_Connector_Connection_Response_Message[]
     */
    public function getSuccessEntities()
    {
        $messages = array();

        foreach ($this->getEntities() as $message) {
            $message->isSuccess() && $messages[] = $message;
        }

        return $messages;
    }

    /**
     * @return Ess_M2ePro_Model_Connector_Connection_Response_Message[]
     */
    public function getNoticeEntities()
    {
        $messages = array();

        foreach ($this->getEntities() as $message) {
            $message->isNotice() && $messages[] = $message;
        }

        return $messages;
    }

    // ########################################

    public function hasErrorEntities()
    {
        return count($this->getErrorEntities()) > 0;
    }

    public function hasWarningEntities()
    {
        return count($this->getWarningEntities()) > 0;
    }

    public function hasSuccessEntities()
    {
        return count($this->getSuccessEntities()) > 0;
    }

    public function hasNoticeEntities()
    {
        return count($this->getNoticeEntities()) > 0;
    }

    // ########################################

    public function hasSystemErrorEntity()
    {
        foreach ($this->getErrorEntities() as $message) {
            if ($message->isSenderSystem()) {
                return true;
            }
        }

        return false;
    }

    public function getCombinedErrorsString()
    {
        $messages = array();

        foreach ($this->getErrorEntities() as $message) {
            $messages[] = $message->getText();
        }

        return !empty($messages) ? implode(', ', $messages) : null;
    }

    public function getCombinedSystemErrorsString()
    {
        $messages = array();

        foreach ($this->getErrorEntities() as $message) {

            if (!$message->isSenderSystem()) {
                continue;
            }

            $messages[] = $message->getText();
        }

        return !empty($messages) ? implode(', ', $messages) : null;
    }

    //########################################
}