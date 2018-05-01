<?php

class MDN_Mpm_Block_System_Config_Button_Carl_AmazonMws extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        try
        {
            $this->setElement($element);

            $html = '<table border="1" cellspacing="0" cellpadding="3" width="800">';

            $channels = Mage::helper('Mpm/Carl')->getChannelsSubscribed();
            foreach($channels as $channel)
            {
                if ($channel->organization == 'amazon') {
                    $credentials = Mage::helper('Mpm/Carl')->getWebserviceCredentials($channel->channelCode);

                    $html .= '<tr><td colspan="2" align="center" bgcolor="#cccccc"><b>' . $channel->channelLabel . '</b></td></tr>';
                    $html .= '<tr><td width="50%">Merchant ID</td><td><input type="text" name="groups[repricing][fields][seller_webservice_' . $channel->channelCode . '_merchant_id][value]" value="' . $credentials->MERCHANT_ID . '" size="50"></td></tr>';
                    $html .= '<tr><td width="50%">Access key ID</td><td><input type="text" name="groups[repricing][fields][seller_webservice_' . $channel->channelCode . '_access_key][value]" value="' . $credentials->AWS_ACCESS_KEY_ID . '" size="50"></td></tr>';
                    $html .= '<tr><td width="50%">Secret key</td><td><input type="password" name="groups[repricing][fields][seller_webservice_' . $channel->channelCode . '_secret_key][value]" value="' . $credentials->AWS_SECRET_ACCESS_KEY . '" size="50"></td></tr>';
                }
            }

            $html .= '</table>';

            return $html;
        }
        catch(Exception $ex)
        {
            return $ex->getMessage();
        }
    }
}