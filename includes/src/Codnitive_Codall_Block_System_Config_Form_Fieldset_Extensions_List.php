<?php
/**
 * CODNITIVE
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE_EULA.html.
 * It is also available through the world-wide-web at this URL:
 * http://www.codnitive.com/en/terms-of-service-softwares/
 * http://www.codnitive.com/fa/terms-of-service-softwares/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   Codnitive
 * @package    Codnitive_Codall
 * @author     Hassan Barza <support@codnitive.com>
 * @copyright  Copyright (c) 2012 CODNITIVE Co. (http://www.codnitive.com)
 * @license    http://www.codnitive.com/en/terms-of-service-softwares/ End User License Agreement (EULA 1.0)
 */

class Codnitive_Codall_Block_System_Config_Form_Fieldset_Extensions_List
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html      = $this->_getHeaderHtml($element);
        $modules   = Mage::getConfig()->getNode('modules')->children();
        $linkTitle = Mage::helper('codall')->__('Goto Extension Page');
        
        foreach ($modules as $moduleName => $values) {
            if (0 !== strpos($moduleName, 'Codnitive_')) {
                continue;
            }
            if($moduleName == 'Codnitive_Notification'){
                continue;
            }
            if ($values->title) {
                $moduleName = (string) $values->title;
            }
            
            $field = $element->addField($moduleName, 'label', array(
                'label' => $moduleName,
                'value' => (string) $values->version
            ));
            $html .= $field->toHtml();
        }
        
        $html .= $this->_getFooterHtml($element);

        return $html;
    }
}
