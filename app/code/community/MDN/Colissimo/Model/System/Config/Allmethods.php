<?php 
/**
 * Description
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 0.1.0
 * @package MDN\Module\Object
 */
class MDN_Colissimo_Model_System_Config_Allmethods
{
    public function toOptionArray($isActiveOnlyFlag=false)
    {
        $methods = array(array('value'=>'', 'label'=>''));
        $carriers = Mage::getSingleton('shipping/config')->getAllCarriers();
        foreach ($carriers as $carrierCode=>$carrierModel) {
        	if (!preg_match('/colissimo/', $carrierCode)) {
	            if (!$carrierModel->isActive() && (bool)$isActiveOnlyFlag === true) {
	                continue;
	            }
	            $carrierMethods = $carrierModel->getAllowedMethods();
	            if (!$carrierMethods) {
	                continue;
	            }
	            $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');
	           
	        	$methods[$carrierCode] = array(
	                'label'   => $carrierTitle,
	                'value' => array(),
	            );	
	            foreach ($carrierMethods as $methodCode=>$methodTitle) {
	                $methods[$carrierCode]['value'][] = array(
	                    'value' => $carrierCode.'_'.$methodCode,
	                    'label' => '['.$carrierCode.'] '.$methodTitle,
	                );
	            }
            }
        }

        return $methods;
    }
}