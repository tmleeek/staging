<?php

class TBT_Rewards_Block_System_Config_Platform_MessageLogin extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getId();
        $html = "";
        
        $connectMessage =  Mage::helper ( 'rewards' )->__ ("Connect to your Sweet Tooth account to manage billing and view analytics!");        
        $kbLink = "https://support.sweettoothrewards.com/entries/21382161-sweet-tooth-account-overview";
        $connectHtmlContents = '<b>'.$connectMessage.'</b>&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$kbLink.'" target="_blank">Learn More</a>';
        
        $signupMessage = Mage::helper ( 'rewards' )->__ ("Create your Sweet Tooth account below:");
        $signupHtmlContents = '<b>'.$signupMessage.'</b>';
        
        $alreadyConnectedMessage = Mage::helper('rewards')->__("Your Sweet Tooth account is connected:");
        $alreadyConnectedHtmlContents = '<b>' . $alreadyConnectedMessage . '</b>';
        
        $connectHtml = '<div id="rewards_connect_message" style="margin-top:5px; margin-bottom:10px;">'.$connectHtmlContents.'</div>';
        $alreadyConnectedHtml = '<div id="rewards_connect_message" style="margin-top:5px; margin-bottom:10px;">'.$alreadyConnectedHtmlContents.'</div>';
        
        
        if (!Mage::getStoreConfig('rewards/platform/is_connected')) {
            $html = $connectHtml;
            
            $configTabJS = <<<FEED
                <script type="text/javascript">//<![CDATA[
                function showConnect() {
                    $('row_rewards_platform_email').hide();
                    $('row_rewards_platform_confirmation').hide();
                    $('btn_signup').hide();
                    $('btn_connect').show();
                    $('row_signup').hide();
                    $('row_connect').show();
                    $('rewards_connect_message').innerHTML = '{$connectHtmlContents}'; 
                }
                
                function showSignup() {
                    $('row_rewards_platform_email').show();
                    $('row_rewards_platform_confirmation').show();
                    $('btn_connect').hide();
                    $('btn_signup').show();
                    $('row_connect').hide(); 
                    $('row_signup').show();
                    $('rewards_connect_message').innerHTML = '{$signupHtmlContents}';
                }
                
                //]]></script>
   
                                         
FEED;
            $html .= $configTabJS;
        } else {
            $html = $alreadyConnectedHtml;
        }
         
        return $html;
    }
   
}