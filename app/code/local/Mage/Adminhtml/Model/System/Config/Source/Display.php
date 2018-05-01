<?php
/*
 * **********************************************************

 * Component Name: Apptha Chatplugin
 * Version: 1.0
 * Edited By: Deepa.S.S.
 * Author URI: http://www.contussupport.com/

 *  **********************************************************

  @Copyright Copyright (C) 2010-2011 Contus Support
  @license GNU/GPL http://www.gnu.org/copyleft/gpl.html,

 * ******************************************************** */
class Mage_Adminhtml_Model_System_Config_Source_display
{

    public function toOptionArray()
    {
        return array(
            array('value' => standard, 'label'=>Mage::helper('adminhtml')->__('Standard')),
            array('value' => bottom, 'label'=>Mage::helper('adminhtml')->__('Window right bottom corner')),
           
        );
    }

}
