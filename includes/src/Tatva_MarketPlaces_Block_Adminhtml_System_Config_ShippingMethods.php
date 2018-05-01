<?php
/**
 * created : 30 septembre 2009
 *
 * Block System configuration
 *
 * @category SQLI
 * @package Tatva_MarketPlaces
 * @author alay
 * @copyright SQLI - 2009 - http://www.tatva.com
 */

/**
 *
 * @package Tatva_MarketPlaces
 */
class Tatva_MarketPlaces_Block_Adminhtml_System_Config_ShippingMethods
    extends Tatva_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	protected $_countries = null;


    public function __construct()
    {
    	$this->addColumn('shipping_code', array(
            'label' => Mage::helper('adminhtml')->__('Modes partner'),
            'style' => 'width:200px',
        	'type' => 'text'
        ));

        $this->addColumn('shipping_mapping', array(
            'label' => Mage::helper('adminhtml')->__('Shipping Methods'),
            'style' => 'width:150px',
        	'type' => 'select',
        	'values' => $this->shippingMethodsToOptionHtml(),
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add New Text');
        parent::__construct();
    }

    public function shippingMethodsToOptionHtml()
    {
    	$activeCarriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach($activeCarriers as $carrierCode => $carrierModel)
        {
           $options = array();
           if( $carrierMethods = $carrierModel->getAllowedMethods() )
           {
               foreach ($carrierMethods as $methodCode => $method)
               {
                    $code= $carrierCode.'_'.$methodCode;
                    $options[]=array('value'=>$code,'label'=>$method);

               }
               $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');

           }
            $methods[]=array('value'=>$options,'label'=>$carrierTitle);
        }
        return  $methods;
    }

}