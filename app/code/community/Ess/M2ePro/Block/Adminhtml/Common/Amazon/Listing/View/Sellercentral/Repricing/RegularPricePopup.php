<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Common_Amazon_Listing_View_Sellercentral_Repricing_RegularPricePopup
    extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('amazonRepricingRegularPricePopup');
        // ---------------------------------------

        $this->setTemplate('M2ePro/common/amazon/listing/view/sellercentral/repricing/regular_price_popup.phtml');
    }

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        $data = array(
            'class'   => 'confirm-action',
            'label'   => Mage::helper('M2ePro')->__('Confirm'),
        );
        $this->setChild(
            'yes_btn',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData($data)
        );

        return $this;
    }
}