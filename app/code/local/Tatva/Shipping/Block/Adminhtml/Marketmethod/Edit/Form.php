<?php

/**
 * 
 * @package Tatva_Shipping
 */

class Tatva_Shipping_Block_Adminhtml_Marketmethod_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
    /**
    * Formulaire de saisie d'une zone
    */
    protected function _prepareForm()
    {

  	    $form = new Varien_Data_Form(
  	 	    array('id' => 'edit_form',
                  'action' =>
  	 				$this->getUrl('*/*/save',
		   				array('shipping_marketmethod_id' => $this->getRequest()->getParam('shipping_marketmethod_id'))),
                  'enctype' => 'multipart/form-data',
		          'method' => 'post'
                ));

        $fieldset = $form->addFieldset('shipping_marketmethod_form',
      	    array('legend'=>Mage::helper('tatvashipping')->__('Add New Rule For MarketPlace') )
        );

        $fieldset->addField('shipping_code_amazon', 'text', array(
            'label'     => Mage::helper('tatvashipping')->__('Shipping Amazone %'),
            'title'     => Mage::helper('tatvashipping')->__('Shipping Amazone %'),
            'required'  => true,
            'name'      => 'shipping_code_amazon',
        ));

        $fieldset->addField('shipping_code_ebay', 'text', array(
            'label'     => Mage::helper('tatvashipping')->__('Shipping Ebay %'),
            'title'     => Mage::helper('tatvashipping')->__('Shipping Ebay %'),
            'required'  => true,
            'name'      => 'shipping_code_ebay',
        ));

        $fieldset->addField('market_weight_from', 'text', array(
            'label'     => Mage::helper('tatvashipping')->__('Weight From'),
            'title'     => Mage::helper('tatvashipping')->__('Weight From'),
            'required'  => true,
            'name'      => 'market_weight_from',
        ));

        $fieldset->addField('market_weight_to', 'text', array(
            'label'     => Mage::helper('tatvashipping')->__('Weight To'),
            'title'     => Mage::helper('tatvashipping')->__('Weight To'),
            'required'  => true,
            'name'      => 'market_weight_to',
        ));

        $fieldset->addField('market_ordertotal_from', 'text', array(
            'label'     => Mage::helper('tatvashipping')->__('Order Total From'),
            'title'     => Mage::helper('tatvashipping')->__('Order Total From'),
            'required'  => true,
            'name'      => 'market_ordertotal_from',
        ));

        $fieldset->addField('market_ordertotal_to', 'text', array(
            'label'     => Mage::helper('tatvashipping')->__('Order Total To'),
            'title'     => Mage::helper('tatvashipping')->__('Order Total To'),
            'required'  => true,
            'name'      => 'market_ordertotal_to',
        ));

        $fieldset->addField('market_shipping_code', 'select', array(
                'name'      => 'market_shipping_code',
                'label'     => Mage::helper('tatvashipping')->__('MarketPlace Method'),
                'title'     => Mage::helper('tatvashipping')->__('MarketPlace Method'),
                'required'  => true,
                'values'    => $this->getShippingMethodsName(),
            ));

        $fieldset->addField('countries_ids', 'multiselect', array(
                'name'      => 'countries_ids[]',
                'label'     => Mage::helper('tatvashipping')->__('Countries'),
                'title'     => Mage::helper('tatvashipping')->__('Countries'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_config_source_country')->toOptionArray(),
            ));

        $form->setValues(Mage::registry('shipping_marketmethod_data')->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getShippingMethodsName()
    {
        $method_array = array();
        $methods = mage::helper('Orderpreparation/ShippingMethods')->getArray();
        foreach($methods as $key => $label)
        {
            $method_array[]= array('value'=>$key,'label'=>$key);
        }

        return $method_array;
    }
}