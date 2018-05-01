<?php

class TBT_Rewards_Block_System_Config_Platform_Create extends TBT_Rewards_Block_System_Config_Abstractbutton
{
    public function getButtonData($buttonBlock)
    {
        $separator = "?";
        $url = Mage::helper('adminhtml')->getUrl('rewardsadmin/manage_config_platform/signup');
        if (strpos($url, $separator) !== false) {
            $separator = "&";
        }
        $url .= $separator;
        
        $onClickJs = <<<FEED
                Validation.addAllThese([    
                    ['validate-loginpassword', 'Please make sure your passwords match.', function(v) {
                        var conf = $('confirmation') ? $('confirmation') : $$('.validate-loginpassword')[0];
                        var pass = false;
                        if ($('rewards_platform_password')) {
                            pass = $('rewards_platform_password');
                        }
                        return (pass.value == conf.value);
                    }]
                ]);
                var valid = Validation.validate('rewards_platform_username');
                valid += Validation.validate('rewards_platform_email');
                valid += Validation.validate('rewards_platform_password');
                valid += Validation.validate('rewards_platform_confirmation');
        
                if (valid == 4) {
                    $('rewards_signup_loading').style.display = '';
                    var platformConnectUri='{$url}';
                    platformConnectUri += 'username=' + $('rewards_platform_username').value + '&';
                    platformConnectUri += 'email=' + $('rewards_platform_email').value + '&';
                    platformConnectUri += 'password=' + $('rewards_platform_password').value;
                    setLocation(platformConnectUri);
                }
FEED;
        
        $loadingMessage = $this->__("We're creating your account. This process may take a few minutes to complete...");
        $afterHtml = <<<FEED
            <span id="rewards_signup_loading" style="display: none;">
                <br/>
                <img src="{$this->getSkinUrl('images/rule-ajax-loader.gif')}"
                    style="margin-right:5px; margin-top:10px;" />
                
                {$loadingMessage}
            </span>
FEED;
        
        $data = array(
            'label'      => Mage::helper('rewards')->__("Create Account and Connect"),
            'onclick'    => $onClickJs,
            'class'      => "",
            'comment'    => "",
            'id'         => "btn_signup",
            'after_html' => $afterHtml
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
}
