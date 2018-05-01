<?php

class MDN_Mpm_Block_Shippings_Main extends Mage_Adminhtml_Block_Widget_Grid
{

    private $clientCurrency = null;

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/Save');
    }

    public function getShippingsGrid()
    {
        return Mage::helper('Mpm/Carl')->getShippingGrids();
    }

    public function getTranslateJson() {
        $translations = array(
            'Delete' => $this->__('Delete'),
            "Grid name must have at least %s caracters" => $this->__('Grid name must have at least %s caracters'),
            'Add row' => $this->__('Add row'),
            'Weight' => $this->__('Weight') . ' ( ' . $this->__('Bigger than') . ' )',
            'Cost' =>$this->__('Cost'),

        );
        return Mage::helper('core')->jsonEncode($translations);
    }

    public function getClientCurrency()
    {
        if($this->clientCurrency === null){
            $this->clientCurrency =  Mage::helper('Mpm/Carl')->getClientCurrency();
        }
        return $this->clientCurrency;
    }
}
