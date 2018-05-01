<?php
class Tatva_Customerattributes_Block_Adminhtml_Widget_Grid_Column_Renderer_Multiselectattributes extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $options = $this->getColumn()->getOptions();
        $showMissingOptionValues = (bool)$this->getColumn()->getShowMissingOptionValues();
        if (!empty($options) && is_array($options)) {
            $value = $row->getData($this->getColumn()->getIndex());
            if (is_array($value)) {
                $res = array();
                foreach ($value as $item) {
                    if (isset($options[$item])) {
                        $res[] = $options[$item];
                    }
                    elseif ($showMissingOptionValues) {
                        $res[] = $item;
                    }
                }
                return implode(', ', $res);
            }
            elseif (isset($options[$value])) {
                return $options[$value];
            } elseif (is_string($value)) { // <--- MY CHANGES HERE
                $values = explode(',', $value);
                $returnOptions = "";
                foreach($values as $k=>$v) {
                    $returnOptions .= $options[$v]. ", ";
                }
                return substr($returnOptions, 0, -2);
            }
            return '';
        }
    }
}