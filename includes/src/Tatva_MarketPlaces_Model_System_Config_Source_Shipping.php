<?php class Tatva_MarketPlaces_Model_System_Config_Source_Shipping
{
    public function toOptionArray()
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
?>