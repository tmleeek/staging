<?php

class TBT_Rewards_Block_Billboard_Nomerchant extends TBT_Billboard_Block_Billboard
{

    protected function _beforeToHtml() {
        $title = $this->hasData( 'title' ) ? $this->getData( 'title' ) : $this->__("Thanks for installing. You just need to create your account or log-in...");
        $displayContinueLink = $this->hasData('displayContinueLink') ? $this->getData('displayContinueLink') : true;
        $this->setTitle( $title )->setDisplayContinueLink( false );
        
        parent::_beforeToHtml();
        
        $this->_sections[] = $this->_getSection1Block();
        
        
        return $this;
    }
    
    /**
     * @return Mage_Core_Block_Abstract section 1
     */
    protected function _getSection1Block() {
        $section1_body_string = $this->__("Log-in or create your account. (login or create account snippet will go here)");
        $section1_body = Mage::helper('tbtcommon/strings')->getTextWithLinks($section1_body_string, 'signup_link', $this->getPdcMerchantAppUrl(), array('target'=>'pdc_merchant_app'));
        
        $block = $this->getLayout()
            ->createBlock( 'tbtbillboard/billboard_section' )
            ->setData( 'content', $section1_body);
        
        return $block;
    }
    
    
    
    
    /**
     * @return string
     */
    public function getConfigSectionUrl() {
        return $this->getUrl('adminhtml/system_config/edit/section/rewards');
    }
    
    /**
     * TODO update this with the live URL, or make a live/dev mode, or put it in the CFG.
     * @return string
     */
    public function getPdcMerchantAppUrl() {
        return "https://magento.points.com/sweettooth/enroll.html#start_merchant_application";
    }
}
