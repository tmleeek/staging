<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Behaviour extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $product)
    {
        $disableText = "";
        if($product->getstatus() === MDN_Mpm_Model_Pricer::kPricingStatusError){
            $disableText = "disabled";
        }

        $html = '<select ' . $disableText .  ' class="behaviour" id="behavior_'.$product->getproduct_id().'" onchange="updateProductData(\''
            .$product->getproduct_id()
            .'\',
        \''.$product->getChannel().'\',
        \'behavior\', this.value)">';
        foreach(Mage::getSingleton('Mpm/System_Config_Behaviour')->getAllOptions() as $behavior) {
            $html .= '<option value="'.$behavior['value'].'" '.($behavior['value'] == $product->getBehavior() ? ' selected ' : '').'>'.$behavior['label'].'</option>';
        }
        $html .= '</select>';

        return $html;
    }

    public function renderExport(Varien_Object $product)
    {
        return $product->getBehaviour();
    }

}