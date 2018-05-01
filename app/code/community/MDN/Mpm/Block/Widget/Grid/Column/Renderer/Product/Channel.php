<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Channel extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $product)
    {
        $channel = $this->getColumn()->getindex();
        $reference = Mage::helper('Mpm/MarketPlace')->getMpReference($channel, $product);
        if (!$reference) {
            return '<center>n/a</center>';
        }

        $product = Mage::getModel('catalog/product')->load($product->getId());
        try
        {
            $offers = Mage::helper('Mpm/Product')->getOffers($product, false, $channel);
            $settings = Mage::getSingleton('Mpm/Product_Setting')->getForProductChannel($product->getId(), $channel);
            $bestOffer = Mage::helper('Mpm/Product')->getBestOffer($offers, $channel);

            $priceResult = Mage::getSingleton('Mpm/Pricer')->calculatePrice($product, $channel);
            $priceDebug = Mage::getSingleton('Mpm/Pricer')->_debug;

            $behaviours = Mage::getSingleton('Mpm/System_Config_Behaviour')->getAllOptions();

            $color = Mage::helper('Mpm/Pricing')->getColorForRepricingStatus($priceDebug['status']);
            $productName = str_replace('"', "", str_replace("'", "", $product->getName()));

            $html = '<div id="pricing_'.$channel.'_'.$product->getId().'" onclick="highLightChannel(this); openMyPopup(\''.$this->getUrl('*/*/offersPopup', array('product_id' => $product->getId(), 'channel' => $channel)).'\', \''.$channel.' - '.$productName.'\')">';
            $html .= '<table class="emptytable" border="0">';
            $html .= '<tr><td align="center" width="50%"><div class="mpm-no-wrap"><font color="'.$color.'">'.($bestOffer ? number_format($bestOffer->getTotal(), 2, '.', '') : '-').'<br>'.($bestOffer ? Mage::helper('Mpm/Tools')->truncateText($bestOffer->getSellerName(), 13) : '-').'</color></div>';
            $html .= '</td>';
            $simulatedRank = Mage::helper('Mpm/Product')->simulateRank($product->getId(), $channel, $priceDebug['result']);
            $html .= '<td align="center" width="50%"><div class="mpm-no-wrap"><font color="'.$color.'">'.number_format($priceDebug['result'], 2, '.', '').'</color> (#'.$simulatedRank.')<br><i>'.$priceDebug['behaviour'].'</i></div></td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '</div>';
        }
        catch(Exception $ex)
        {
            $html = '<font color="red">'.$ex->getMessage().'</font>';
        }
        return $html;
    }

}