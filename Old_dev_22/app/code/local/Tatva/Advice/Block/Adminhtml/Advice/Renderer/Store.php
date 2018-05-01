<?php
/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2009 AITOC, Inc.
*/

class Tatva_Advice_Block_Adminhtml_Advice_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $value_data=array();  $material_label=array(); $materila_data_final='';
        $value_data=explode(',',$value);
        if(is_array($value_data))
        {
          foreach($value_data  as $datas)
          {
             if($datas!='')
             {
              $material_label[]=Mage::getModel('core/store')->load($datas)->getName();
             }
         }
        }
      $materila_data_final=implode(',',$material_label);
      return $materila_data_final;
    }
 }