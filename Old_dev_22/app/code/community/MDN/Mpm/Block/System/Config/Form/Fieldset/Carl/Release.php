<?php

/**
 * Class MDN_Mpm_Block_System_Config_Form_Carl_Release
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_System_Config_Form_Fieldset_Carl_Release extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    /**
     * @param \Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {

        $version = Mage::Helper('Mpm')->getInstalledVersion();
        $html = $this->_getHeaderHtml($element);

        $html .= '<table cellspacing="0" class="form-list">';
        $html .= '<colgroup class="label"></colgroup>';
        $html .= '<colgroup class="value"></colgroup>';
        $html .= '<colgroup class="scope-label"></colgroup>';
        $html .= '<colgroup></colgroup>';
        $html .= '<tbody>';
        $html .= '<tr><td class="label">'.$this->__('Version').'</td>';
        $html .= '<td class="value">'.$version['installed_version'].'</td>';
        $html .= '<td class="scope-label"></td>';
        $html .= '<td></td></tr></tbody></table>';

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

}