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

class Aitoc_Aitmanufacturers_Helper_Manufacturer extends Mage_Core_Helper_Abstract
{
    /**
    * Renders page
    *
    * Call from controller action
    *
    * @param Mage_Core_Controller_Front_Action $action
    * @param integer $pageId
    * @return boolean
    */
    public function renderPage(Mage_Core_Controller_Front_Action $action, $id=null, $attributeId)
    {
        $model = Mage::getSingleton('aitmanufacturers/aitmanufacturers');
        if (!is_null($id) && $id!==$model->getId()) {
            if (!$model->load($id)) {
                return false;
            }
        }

        if (!$model->getId() OR $model->getStatus() != 1) {
            return false;
        }

        $action->loadLayout(array('default', 'aitmanufacturers_index_view'), false, false);
        $action->getLayout()->getUpdate()->addUpdate($model->getLayoutUpdateXml());
        $action->getLayout()->helper('page/layout')->applyHandle($model->getRootTemplate());
        $action->generateLayoutXml()->generateLayoutBlocks();

        if ($storage = Mage::getSingleton('catalog/session')) {
            $action->getLayout()->getMessagesBlock()->addMessages($storage->getMessages(true));
        }

        if ($storage = Mage::getSingleton('checkout/session')) {
            $action->getLayout()->getMessagesBlock()->addMessages($storage->getMessages(true));
        }
        if (('aitmanufacturers_index_view' == $action->getFullActionName()) && Mage::helper('aitmanufacturers')->canUseLayeredNavigation($attributeId))
        {
            /** 
             * @author ksenevich
             */
            Mage::dispatchEvent('aitmanufacturers_render_adjnav', array(
                'action' => $action, 
                ));
        }

        $action->renderLayout();
        return true;
    }
    
    public function getActiveAttributes()
    {
        $aActiveAttributes = array();
        foreach ($this->getAttributes() as $code => $attribute)
        {
            if(Mage::helper('aitmanufacturers')->getIsActive($code) && Mage::helper('aitmanufacturers')->getConfigParam('include_in_navigation_menu', $code))
            {
                $aActiveAttributes[$code]= $attribute;
            }
        }
        return $aActiveAttributes;
    }
    
    public function getAttributes()
    {
        return Mage::getResourceModel('aitmanufacturers/config')->getAttributeList();
    }
      
}