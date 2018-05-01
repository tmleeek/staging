<?php

/**
 * Class MDN_Mpm_Block_Rules_Main
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Main extends Mage_Adminhtml_Block_Widget_Container
{

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl('*/*/Edit');
    }

    /**
     * @return string
     */
    public function getRulesTypesAsCombo(){

        $html = '<select id="ruleTypesSelect" style="display:none;" onchange="Rule.add(this.value);">';
        $html .= '<option></option>';

        foreach(Mage::getSingleton('Mpm/System_Config_RuleTypes')->getAllOptions() as $option){

            $html .= '<option value="'.$option['value'].'">'.$option['label'].'</option>';

        }

        $html .= '</select>';

        return $html;

    }
}
