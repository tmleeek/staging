<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   1.0.1
 * @revision  135
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Rma_Model_Config_Source_Field_Type
{

    public function toArray()
    {
        return array(
            Mirasvit_Rma_Model_Config::FIELD_TYPE_TEXT => Mage::helper('rma')->__('Text'),
            Mirasvit_Rma_Model_Config::FIELD_TYPE_TEXTAREA => Mage::helper('rma')->__('Multi-line text'),
            Mirasvit_Rma_Model_Config::FIELD_TYPE_DATE => Mage::helper('rma')->__('Date'),
            Mirasvit_Rma_Model_Config::FIELD_TYPE_CHECKBOX => Mage::helper('rma')->__('Checkbox'),
            Mirasvit_Rma_Model_Config::FIELD_TYPE_SELECT => Mage::helper('rma')->__('Drop-down list'),
        );
    }
    public function toOptionArray()
    {
        $result = array();
        foreach($this->toArray() as $k=>$v) {
            $result[] = array('value'=>$k, 'label'=>$v);
        }
        return $result;
    }

    /************************/
}