<?php

class MDN_Mpm_Block_System_Config_Button_Carl_CategoriesToExport extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $values = explode(',', Mage::getStoreConfig('mpm/repricing/categories'));

        $html = '<select id="mpm_repricing_categories" name="groups[repricing][fields][categories][value][]" multiple="multiple" style="width: 800px" size="20">';

        $html .= '<option value="*">'.$this->__('All').'</option>';

        $categories = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('*')->setOrder('path', 'asc');
        foreach($categories as $category)
        {
            $selected = in_array($category->getId(), $values);
            $fullPath = Mage::helper('Mpm/Category')->getCategoryFullPathName($category, true);
            if ($fullPath)
                $html .= '<option '.($selected ? ' selected ' : '').' value="'.$category->getId().'">'.$fullPath.' (id:'.$category->getId().')</option>';
        }

        $html .= '</select>';

        return $html;
    }

}