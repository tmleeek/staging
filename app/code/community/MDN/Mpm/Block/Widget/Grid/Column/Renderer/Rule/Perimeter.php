<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Rule_Perimeter extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $html = '';
        foreach($row->perimeter as $field => $value) {
            $field = str_replace('attributes.global.', '', $field);
            if(is_array($value)) {
                $html.= $field.' : '.implode(', ', $value).'<br />';
            } else {
                $html.= $field.' : '.$value.'<br />';
            }
        }

        return $html;
    }

    public function getHumanReadablePerimeter()
    {
        $html = array();

        //main fields
        foreach($this->getPerimeterCondition('*') as $field => $list)
        {
            if (!$this->isWildcard($list))
            {
                if ($list != '') {
                    $list = $this->convertListToHuman($field, $list);
                    $html[] = $field . ' : ' . implode(', ', $list);
                }
            }
        }

        if (count($html) > 0)
            return implode('<br>', $html);
        else
            return Mage::helper('Mpm')->__('All products');
    }

    public function convertListToHuman($field, $list)
    {
        $finalList = array();

        switch($field)
        {
            case 'stock':
                $finalList[] = 'between '.$list['from'].' and '.$list['to'];
                break;
            case 'sku':
                $finalList[] = 'in '.$list;
                break;
            case 'categories':
                foreach($list as $item) {
                    $finalList[] = Mage::getModel('catalog/category')->load($item)->getName();
                }
                break;
            case 'attributesets':
                foreach($list as $item) {
                    $finalList[] = Mage::getModel('eav/entity_attribute_set')->load($item)->getAttributeSetName();
                }
                break;
            case 'channels':
                foreach($list as $item) {
                    $finalList[] = $item;
                }
                break;
            default:    //attributes !
                switch(Mage::helper('Mpm/Attribute')->getFrontEndInput($field))
                {
                    case 'text':
                        $finalList[] = 'Contains "'.$list.'"';
                        break;
                    case 'boolean':
                        $finalList[] = ($list ? 'Yes' : 'No');
                        break;
                    case 'date':
                    case 'weight':
                    case 'datetime':
                    case 'price':
                        $finalList[] = 'between '.$list['from'].' and '.$list['to'];
                        break;
                    case 'select':
                    case 'multiselect':
                        foreach($list as $item) {
                            $finalList[] = Mage::helper('Mpm/Attribute')->getAttributeValueLabel($field, $item);
                        }
                        break;
                }
                break;
        }

        return $finalList;
    }

}