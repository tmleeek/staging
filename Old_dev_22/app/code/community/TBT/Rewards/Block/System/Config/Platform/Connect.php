<?php

class TBT_Rewards_Block_System_Config_Platform_Connect extends TBT_Rewards_Block_System_Config_Abstractbutton
{
    public function getButtonData($buttonBlock)
    {
        $separator = "?";
        $url = Mage::helper('adminhtml')->getUrl('rewardsadmin/manage_config_platform/connect');
        if (strpos($url, $separator) !== false) {
            $separator = "&";
        }
        $url .= $separator;
        
        $onClickJs = <<<FEED
            var valid = Validation.validate('rewards_platform_username');
            valid += Validation.validate('rewards_platform_password');
            
            if (valid == 2) {
                var platformConnectUri='{$url}';
                platformConnectUri += 'username=' + encodeURIComponent($('rewards_platform_username').value) + '&';
                platformConnectUri += 'password=' + encodeURIComponent($('rewards_platform_password').value) + '&';
                platformConnectUri += 'isDevMode=' + $('rewards_platform_dev_mode').value;
                setLocation(platformConnectUri);
            }
FEED;
        
        $afterHtml = $this->_getAfterHtml();
        
        $data = array(
            'label'      => Mage::helper('rewards')->__("Connect to Sweet Tooth"),
            'onclick'    => $onClickJs,
            'class'      => "",
            'comment'    => "",
            'id'         => "btn_connect",
            'after_html' => (Mage::getStoreConfig('rewards/platform/is_connected') ? '' : $afterHtml)
        );
        return $data;
    }
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '';
        
        if (!Mage::getStoreConfig('rewards/platform/is_connected')) {
            $html = parent::render($element);
        }
        
        return $html;
    }
    
    protected function _getAfterHtml()
    {
        $afterHtml = <<<FEED
            <tr id="row_connect">
                <td>&nbsp;</td>
                <td>
                    <div style="margin-top:5px;">
                        Don't have an account? [signup_link]Click here to signup![/signup_link]
                    </div>
                </td>
            </tr>
FEED;
        $afterHtml = Mage::helper('tbtcommon/strings')->getTextWithLinks($afterHtml,
            'signup_link', $this->_getSignupUrl(), array('target' => '_window'));
        
        return $afterHtml;
    }
    
    protected function _getSignupUrl()
    {
        return "http://www.sweettoothrewards.com/pricing/";
    }
}
