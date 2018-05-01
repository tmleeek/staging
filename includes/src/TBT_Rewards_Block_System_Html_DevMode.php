<?php

class TBT_Rewards_Block_System_Html_DevMode extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const CONFIG_API_KEY = 'rewards/platform/apikey';
    const CONFIG_DEV_MODE = 'rewards/platform/dev_mode';
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $apiKey = Mage::getStoreConfig(self::CONFIG_API_KEY);
        $isDevMode = Mage::getStoreConfig(self::CONFIG_DEV_MODE);
        
        if (!$apiKey || !$isDevMode) {
            return "";
        }
        
        $notice = $this->__("Notice");
        $message = $this->__("Your account is in %s.  This mode should not be used in a production environment.",
            "<strong>" . $this->__("Developer Mode") . "</strong>"
        );
        $learnMoreText = $this->__("Learn More");
        $learnMoreLink = "https://support.sweettoothrewards.com/entries/21526272-developer-mode";
        
        $html = <<<FEED
            </tbody></table>
            
            <div style="box-shadow: 1px 2px 3px rgba(0, 0, 0, 0.05), inset 0px 1px 0px #fff; text-shadow: 0px 1px 0px #fff; border-radius: 4px; line-height: 16px; width:430px; color: rgba(206, 165, 96, 1); border: 1px solid #d8ab42; background-color: #fffbe8; padding: 6px 12px; font-size: 12px; margin: 20px 0 5px;">
                <strong>{$notice}:</strong> {$message}
                <a href="{$learnMoreLink}" target="_window">{$learnMoreText}</a>
            </div>
            
            <table><tbody>
FEED;
        
        return $html;
    }
}
