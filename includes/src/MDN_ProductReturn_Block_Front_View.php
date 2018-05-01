<?php

class MDN_ProductReturn_Block_Front_View extends Mage_Core_Block_Template
{
    private $_productReturn = null;

    public function getRma()
    {
        if ($this->_productReturn == null) {
            $productReturnId      = $this->getRequest()->getParam('rma_id');
            $this->_productReturn = mage::getModel('ProductReturn/Rma')->load($productReturnId);
        }

        return $this->_productReturn;
    }

    /**
     * return current customer
     *
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getCustomerAddressesAsCombo($name, $value)
    {
        $retour    = '<select name="' . $name . '" id="' . $name . '">';
        $addresses = $this->getCustomer()->getAddresses();
        foreach ($addresses as $address) {
            $selected = '';
            if ($value == $address->getId())
                $selected = ' selected="selected" ';

            $retour .= '<option value="' . $address->getId() . '"  ' . $selected . '>' . $address->getFormated() . '</option>';
        }

        $retour .= '</select>';

        return $retour;
    }

    public function getCustomerAddresses($value)
    {
        $addresses = $this->getCustomer()->getAddresses();
        $retour    = "";
        foreach ($addresses as $address) {
            if ($value == $address->getId())
                $retour = $address->getFormated();
        }

        return $retour;
    }

    public function getReasonsAsCombo($name, $value)
    {
        $retour  = '<select name="' . $name . '" id="' . $name . '">';
        $reasons = $this->getRma()->getReasons();
        foreach ($reasons as $reason) {
            $selected = '';
            if ($value == $reason)
                $selected = ' selected="selected" ';
            $retour .= '<option value="' . $reason . '" ' . $selected . '>' . $this->__($reason) . '</option>';
        }

        $retour .= '</select>';

        return $retour;

    }

    public function getQtySelect($name, $max)
    {
        $retour = '<select name="' . $name . '" id="' . $name . '">';
        for ($i = 0; $i <= $max; $i++) {
            $retour .= '<option value="' . $i . '">' . $i . '</option>';
        }
        $retour .= '</select>';

        return $retour;
    }

    public function getReturnUrl()
    {
        return $this->getUrl('ProductReturn/Front/List');
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('ProductReturn/Front/SubmitRequest');
    }

    public function getReturnCGVUrl()
    {
        return $this->getUrl('ProductReturn/Front/ViewCGV', array('rma_id' => $this->getRma()->getrma_id()));
    }

    public function CustomerCanEdit()
    {
        return $this->getRma()->CustomerCanEdit();
    }

    /**
     * Enter description here...
     *
     */
    public function getProductName($product)
    {
        return mage::getModel('ProductReturn/RmaProducts')->getProductName($product);
    }
}