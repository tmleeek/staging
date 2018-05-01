<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Common_Amazon_Configuration_General_Form extends Mage_Adminhtml_Block_Widget_Form
{
    //########################################

    public function __construct()
    {
        parent::__construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('amazonConfigurationGeneralForm');
        $this->setContainerId('magento_block_amazon_configuration_general');
        $this->setTemplate('M2ePro/common/amazon/configuration/general/form.phtml');
        // ---------------------------------------
    }

    //########################################
}