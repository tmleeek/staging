<?php

class TBT_Milestone_Helper_Config extends Mage_Core_Helper_Abstract
{
    public function getOrdersTrigger($storeId = null)
    {
        return Mage::getStoreConfig('rewards/milestone/orders_trigger', $storeId);
    }

    public function isTriggerOnOrderCreate($milestoneType, $storeId)
    {
        $configPath = "rewards/milestone/{$milestoneType}_trigger"; 
        return ( Mage::getStoreConfig($configPath, $storeId) == 'create' || 
                 Mage::getStoreConfig($configPath, $storeId) == 'order' );
    }

    public function isTriggerOnOrderPayment($milestoneType, $storeId)
    {
        if ($milestoneType == 'revenue') {
            // Revenue milestone is currently only triggered by payment... but someday, maybe not!
            return true;
        }

        $configPath = "rewards/milestone/{$milestoneType}_trigger";
        return Mage::getStoreConfig($configPath, $storeId) == 'payment';
    }

    public function isTriggerOnOrderShipment($milestoneType, $storeId)
    {
        $configPath = "rewards/milestone/{$milestoneType}_trigger";
        return Mage::getStoreConfig($configPath, $storeId) == 'shipment';
    }
    
    public function getEmailTemplate($milestoneType, $storeId)
    {
        $configPath = "rewards/milestone/{$milestoneType}_email_template";
        return Mage::getStoreConfig($configPath, $storeId);
    }

    public function getMailSenderName($storeId)
    {
        $customSender = Mage::helper('rewards/config')->getCustomSender($storeId);
        return Mage::getStoreConfig ( "trans_email/ident_" . $customSender . "/name", $storeId );
    }
    
    public function getMailSenderEmail($storeId)
    {
        $customSender = Mage::helper('rewards/config')->getCustomSender($storeId);
        return Mage::getStoreConfig ( "trans_email/ident_" . $customSender . "/email", $storeId );
    }
    
    /**
     * Reads the global system.xml file for a value
     * 
     * @param string $path, the xPath to the parent node of variable to read
     * @param string $variable, the variable to read
     * @return string value of the variable at the given path
     */
    public function getSystemConfigValue($path, $variable)
    {
        $systemConfig = Mage::getConfig()->loadModulesConfiguration('system.xml')->getXpath($path);
        
        if (!empty($systemConfig) && is_array($systemConfig)){
            $systemConfig = $systemConfig[0];
            if ($systemConfig instanceof Mage_Core_Model_Config_Element){
                $systemConfig = $systemConfig->asArray();
                if (isset($systemConfig[$variable])){
                    return $systemConfig[$variable];
                }
            }
        }
        
        return "";
    }
}
