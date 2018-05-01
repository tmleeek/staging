<?php

class MDN_Mpm_Adminhtml_Mpm_ShippingsController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        if (!Mage::helper('Mpm/Carl')->checkCredentials()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('Please configure Carl credentials first'));
            $this->_redirect('adminhtml/system_config/edit', array('section' => 'mpm'));
        } else {
            Mage::helper('Mpm/Product')->pricingInProgress();
            $this->loadLayout();
            $this->renderLayout();
        }
    }

    public function SaveAction()
    {
        $groups = $this->getRequest()->getPost('groups');
        $grids = $groups['shipping']['fields'];
        unset($grids['configuration']);

        $allGrids = array();
        foreach($grids as $gridKey => $fields) {
            foreach($fields as $key => $value) {
                if(preg_match('/^(price|weight|name)_(.+)$/', $key, $matches)) {
                    $allGrids[$gridKey][$matches[2]][$matches[1]] = $value['value'];
                }
            }
        }

        // delete all grid name
        foreach($allGrids as $key => $rows) {
            $row = current($rows);
            $gridName = $row['name'];
            Mage::helper('Mpm/Carl')->deleteShippingGrid($gridName);
        }

        // create grid rows
        foreach($allGrids as $key => $rows) {
            foreach($rows as $row) {
                Mage::helper('Mpm/Carl')->createShippingRow($row['name'], $row['weight'], $row['price']);
            }
        }

        $this->_redirect('adminhtml/Mpm_Shippings');
    }

    protected function _isAllowed()
    {
        return true;
    }

}