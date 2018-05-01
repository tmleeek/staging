<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order shipment controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

require_once 'Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php';
class MondialRelay_Pointsrelais_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController
{

    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     */
    
	public function getConfigData($field)
	{
        $path = 'carriers/pointsrelais/'.$field;
        return Mage::getStoreConfig($path, Mage::app()->getStore());
	}
    
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('shipment');

        try {
            
            if ($shipment = $this->_initShipment()) {
                
                //Si l'expedition est rÃ©alisÃ© par Mondial Relay, on crÃ©Ã© le tracking automatiquement.
                
                $_order = $shipment->getOrder();
                $_shippingMethod = explode("_",$_order->getShippingMethod());
                
                if ($_shippingMethod[0] == 'pointsrelaiscd')  {
                    
                    // On met en place les paramÃ¨tres de la requÃ¨te
                    
                    $adress = $_order->getShippingAddress()->getStreet();
                    if (!isset($adress[1]))
                    {
                        $adress[1] = '';
                    }
                    $package_weightTmp = $_order->getWeight()*1000;
        
			        if($this->getConfigData('package_weight')){
			        	$package_weightTmp = $package_weightTmp+($this->getConfigData('package_weight'));
			        }
			        
                    if($package_weightTmp < 100){
                    	$package_weightTmp = 100;
                    }
                    $params = array(
                                   'Enseigne'       => $this->getConfigData('enseigne'),
                                   'ModeCol'        => 'CCC',
                                   'ModeLiv'        => 'DRI',
                                   'Expe_Langage'   => 'FR',
                                   'Expe_Ad1'       => trim($this->removeaccents($this->getConfigData('adresse1_enseigne'))),
                                   'Expe_Ad3'       => trim($this->removeaccents($this->getConfigData('adresse3_enseigne'))),
                                   'Expe_Ad4'       => trim($this->removeaccents($this->getConfigData('adresse4_enseigne'))),
                                   'Expe_Ville'     => trim($this->removeaccents($this->getConfigData('ville_enseigne'))),
                                   'Expe_CP'        => $this->getConfigData('cp_enseigne'),
                                   'Expe_Pays'      => trim($this->removeaccents($this->getConfigData('pays_enseigne'))),
                                   'Expe_Tel1'      => '',
                                   'Expe_Tel2'      => '',
                                   'Expe_Mail'      => $this->getConfigData('mail_enseigne'),
                                   'Dest_Langage'   => 'FR',
                                   'Dest_Ad1'       => trim($this->removeaccents($_order->getShippingAddress()->getFirstname() . ' ' . $_order->getShippingAddress()->getLastname())),
                                   'Dest_Ad2'       => trim($this->removeaccents($_order->getShippingAddress()->getCompagny())),
                                   'Dest_Ad3'       => trim($this->removeaccents($adress[0])),
                                   'Dest_Ad4'       => trim($this->removeaccents($adress[1])),                                   
                                   'Dest_Ville'     => trim($this->removeaccents($_order->getShippingAddress()->getCity())),
                                   'Dest_CP'        => $_order->getShippingAddress()->getPostcode(),
                                   'Dest_Pays'      => trim($this->removeaccents($_order->getShippingAddress()->getCountryId())),
                                   'Dest_Tel1'      => $_order->getShippingAddress()->getTelephone(),
                                   'Dest_Mail'      => $_order->getCustomerEmail(),
                                   'Poids'          => $package_weightTmp,
                                   'NbColis'        => '1',
                                   'CRT_Valeur'     => '0',
                                   'LIV_Rel_Pays'   => $_order->getShippingAddress()->getCountryId(),
                                   'LIV_Rel'        => $_shippingMethod[1]
                    );//$_order->getWeight()*1000,
                    //On crÃ©e le code de sÃ©curitÃ©
                    $select = "";
                    foreach($params as $key => $value){
                    	$value = strtr($value,'àáâãäçèéêëìíîïñòóôõöùúûüıÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜİ?!,;.:', 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY      ');
					    $select .= "\t".'<option value="'.$key.'">' . $value.'</option>'."\r\n";
					}
                    
                    $code = implode("",$params);
                    $code .= $this->getConfigData('cle');
                    
                    //On le rajoute aux paramÃ¨tres
                    $params["Security"] = strtoupper(md5($code));
                    
                    // On se connecte
                    $client = new SoapClient("http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL");
                    
                    // Et on effectue la requÃ¨te
                    $expedition = $client->WSI2_CreationExpedition($params)->WSI2_CreationExpeditionResult;
                    
                    Mage::Log('WSI2_CreationExpeditionResult : '.($expedition->STAT));
                    $track = Mage::getModel('sales/order_shipment_track')
                        ->setNumber($expedition->ExpeditionNum)
                        ->setCarrier('Mondial Relay')
                        ->setCarrierCode($_shippingMethod[0])
                        ->setTitle('Mondial Relay')
                        ->setPopup(1);
                    $shipment->addTrack($track);
                }else if ($_shippingMethod[0] == 'pointsrelais')  {
                    
                    // On met en place les paramÃ¨tres de la requÃ¨te
                    
                    $adress = $_order->getShippingAddress()->getStreet();
                    if (!isset($adress[1]))
                    {
                        $adress[1] = '';
                    }
                    $package_weightTmp = $_order->getWeight()*1000;
        
			        if($this->getConfigData('package_weight')){
			        	$package_weightTmp = $package_weightTmp+($this->getConfigData('package_weight'));
			        }
			        
                    if($package_weightTmp < 100){
                    	$package_weightTmp = 100;
                    }
                    $params = array(
                                   'Enseigne'       => $this->getConfigData('enseigne'),
                                   'ModeCol'        => 'CCC',
                                   'ModeLiv'        => '24R',
                                   'Expe_Langage'   => 'FR',
                                   'Expe_Ad1'       => trim($this->removeaccents($this->getConfigData('adresse1_enseigne'))),
                                   'Expe_Ad3'       => trim($this->removeaccents($this->getConfigData('adresse3_enseigne'))),
                                   'Expe_Ad4'       => trim($this->removeaccents($this->getConfigData('adresse4_enseigne'))),
                                   'Expe_Ville'     => trim($this->removeaccents($this->getConfigData('ville_enseigne'))),
                                   'Expe_CP'        => $this->getConfigData('cp_enseigne'),
                                   'Expe_Pays'      => trim($this->removeaccents($this->getConfigData('pays_enseigne'))),
                                   'Expe_Tel1'      => '',
                                   'Expe_Tel2'      => '',
                                   'Expe_Mail'      => $this->getConfigData('mail_enseigne'),
                                   'Dest_Langage'   => 'FR',
                                   'Dest_Ad1'       => trim($this->removeaccents($_order->getShippingAddress()->getFirstname() . ' ' . $_order->getShippingAddress()->getLastname())),
                                   'Dest_Ad2'       => trim($this->removeaccents($_order->getShippingAddress()->getCompagny())),
                                   'Dest_Ad3'       => trim($this->removeaccents($adress[0])),
                                   'Dest_Ad4'       => trim($this->removeaccents($adress[1])),                                   
                                   'Dest_Ville'     => trim($this->removeaccents($_order->getShippingAddress()->getCity())),
                                   'Dest_CP'        => $_order->getShippingAddress()->getPostcode(),
                                   'Dest_Pays'      => trim($this->removeaccents($_order->getShippingAddress()->getCountryId())),
                                   'Dest_Tel1'      => '',
                                   'Dest_Mail'      => $_order->getCustomerEmail(),
                                   'Poids'          => $package_weightTmp,
                                   'NbColis'        => '1',
                                   'CRT_Valeur'     => '0',
                                   'LIV_Rel_Pays'   => $_order->getShippingAddress()->getCountryId(),
                                   'LIV_Rel'        => $_shippingMethod[1]
                    );//$_order->getWeight()*1000,
                    //On crÃ©e le code de sÃ©curitÃ©
                    
                    
                    $select = "";
                    foreach($params as $key => $value){
                    	$value = strtr($value,'àáâãäçèéêëìíîïñòóôõöùúûüıÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜİ?!,;.:', 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY      ');
					    $select .= "\t".'<option value="'.$key.'">' . $value.'</option>'."\r\n";
					}
                    
                    $code = implode("",$params);
                    $code .= $this->getConfigData('cle');
                    
                    //On le rajoute aux paramÃ¨tres
                    $params["Security"] = strtoupper(md5($code));
                    
                    // On se connecte
                    $client = new SoapClient("http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL");
                    
                    // Et on effectue la requÃ¨te
                    $expedition = $client->WSI2_CreationExpedition($params)->WSI2_CreationExpeditionResult;
                    
                    $track = Mage::getModel('sales/order_shipment_track')
                        ->setNumber($expedition->ExpeditionNum)
                        ->setCarrier('Mondial Relay')
                        ->setCarrierCode($_shippingMethod[0])
                        ->setTitle('Mondial Relay')
                        ->setPopup(1);
                    $shipment->addTrack($track);
                }else if (($_shippingMethod[0] == 'pointsrelaisld1') || ($_shippingMethod[0] == 'pointsrelaislds'))  {
                    
                    // On met en place les paramÃ¨tres de la requÃ¨te
                    
                    $adress = $_order->getShippingAddress()->getStreet();
                    if (!isset($adress[1]))
                    {
                        $adress[1] = '';
                    }
                    $package_weightTmp = $_order->getWeight()*1000;
                    $ModeLiv = 'LDR';
                    if(($_shippingMethod[0] == 'pointsrelaislds')){
                    	$ModeLiv = 'LDS';
                    }
                    
                    $packageTmp = $this->getPackage($_order,$ModeLiv);
                    
                    $params = array(
                                   'Enseigne'       => $this->getConfigData('enseigne'),
                                   'ModeCol'        => 'CCC',
                                   'ModeLiv'        => $ModeLiv,
                                   'Expe_Langage'   => 'FR',
                                   'Expe_Ad1'       => trim($this->removeaccents($this->getConfigData('adresse1_enseigne'))),
                                   'Expe_Ad3'       => trim($this->removeaccents($this->getConfigData('adresse3_enseigne'))),
                                   'Expe_Ad4'       => trim($this->removeaccents($this->getConfigData('adresse4_enseigne'))),
                                   'Expe_Ville'     => trim($this->removeaccents($this->getConfigData('ville_enseigne'))),
                                   'Expe_CP'        => $this->getConfigData('cp_enseigne'),
                                   'Expe_Pays'      => trim($this->removeaccents($this->getConfigData('pays_enseigne'))),
                                   'Expe_Tel1'      => '',
                                   'Expe_Tel2'      => '',
                                   'Expe_Mail'      => $this->getConfigData('mail_enseigne'),
                                   'Dest_Langage'   => 'FR',
                                   'Dest_Ad1'       => trim($this->removeaccents($_order->getShippingAddress()->getFirstname() . ' ' . $_order->getShippingAddress()->getLastname())),
                                   'Dest_Ad2'       => trim($this->removeaccents($_order->getShippingAddress()->getCompagny())),
                                   'Dest_Ad3'       => trim($this->removeaccents($adress[0])),
                                   'Dest_Ad4'       => trim($this->removeaccents($adress[1])),                                   
                                   'Dest_Ville'     => trim($this->removeaccents($_order->getShippingAddress()->getCity())),
                                   'Dest_CP'        => $_order->getShippingAddress()->getPostcode(),
                                   'Dest_Pays'      => trim($this->removeaccents($_order->getShippingAddress()->getCountryId())),
                                   'Dest_Tel1'      => trim($this->removeaccents($_order->getShippingAddress()->getTelephone())),
                                   'Dest_Mail'      => $_order->getCustomerEmail(),
                                   'Poids'          => $package_weightTmp,
                                   'NbColis'        => $packageTmp[0],
                                   'CRT_Valeur'     => '0'
                    );
                    //On crÃ©e le code de sÃ©curitÃ©
                    
                    $select = "";
                    foreach($params as $key => $value){
                    	$value = strtr($value,'àáâãäçèéêëìíîïñòóôõöùúûüıÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜİ?!,;.:', 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY      ');
					    $select .= "\t".'<option value="'.$key.'">' . $value.'</option>'."\r\n";
					}
                    
                    $code = implode("",$params);
                    $code .= $this->getConfigData('cle');
                    
                    //On le rajoute aux paramÃ¨tres
                    $params["Security"] = strtoupper(md5($code));
                    
                    // On se connecte
                    $client = new SoapClient("http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL");
                    
                    // Et on effectue la requÃ¨te
                    $expedition = $client->WSI2_CreationExpedition($params)->WSI2_CreationExpeditionResult;
                    
                    $track = Mage::getModel('sales/order_shipment_track')
                        ->setNumber($expedition->ExpeditionNum)
                        ->setCarrier('Mondial Relay')
                        ->setCarrierCode($_shippingMethod[0])
                        ->setTitle('Mondial Relay')
                        ->setPopup(1);
                    $shipment->addTrack($track);
                }
                $shipment->register();
                $comment = '';
                if (!empty($data['comment_text'])) {
                    $shipment->addComment($data['comment_text'], isset($data['comment_customer_notify']));
                    $comment = $data['comment_text'];
                }

                if (!empty($data['send_email'])) {
                    $shipment->setEmailSent(true);
                }

                $this->_saveShipment($shipment);
                $shipment->sendEmail(!empty($data['send_email']), $comment);
                $this->_getSession()->addSuccess($this->__('Shipment was successfully created.'));
                $this->_redirect('adminhtml/sales_order/view', array('order_id' => $shipment->getOrderId()));
                return;
            	
            }else {
                $this->_forward('noRoute');
                return;
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__('Can not save shipment: '.$e->getMessage()));
        }
        $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
    }
    
    Function getPackage($_order,$shipmentType){ 
    	$nbtoreturn = 1;
    	$totalLength = 0;
    	$length = 0;
    	$weight = 0;
    	$lengthTmp = 0;
    	$weightTmp = 0;
    	$decrementFlag = false;
    	foreach ($_order->getAllItems() as $item) {
    			if($decrementFlag){
    				$length += $lengthTmp;
    				$weight += $weightTmp;
    				$decrementFlag = false;
    			}
	            if ($productId = $item->getProductId()) {
	                $product = Mage::getModel('catalog/product')->load($productId);
	                if($product->getDevelopedLength()){
	                	$length += $product->getDevelopedLength();
	                	$lengthTmp = $product->getDevelopedLength();
	                	$totalLength += $product->getDevelopedLength();
	                }else{
	                	$length += $this->getConfigData('default_developed_length');
	                	$lengthTmp = $this->getConfigData('default_developed_length');
	                	$totalLength += $this->getConfigData('default_developed_length');
	                }
	                $weight += $product->getWeight();

	                if($shipmentType == 'LDS'){
	                	if(($weightTmp > 130) || ($lengthTmp > 450)){
	                		$decrementFlag = true;
	                		$nbtoreturn++;
					    	$length = 0;
					    	$weight = 0;
	                	}
	                }else{
	                	if(($weightTmp > 60) || ($lengthTmp > 250)){
	                		$decrementFlag = true;
	                		$nbtoreturn++;
					    	$length = 0;
					    	$weight = 0;
	                	}
	                
	                }
	            }
	    }
		return array($nbtoreturn,$totalLength);
   	}
   
    Function removeaccents($string){ 
	   $stringToReturn = str_replace( 
	   array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ı','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'İ','/','\xa8'), 
	   array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y',' ','e'), $string);
	   // Remove all remaining other unknown characters
	$stringToReturn = preg_replace('/[^a-zA-Z0-9\-]/', ' ', $stringToReturn);
	$stringToReturn = preg_replace('/^[\-]+/', '', $stringToReturn);
	$stringToReturn = preg_replace('/[\-]+$/', '', $stringToReturn);
	$stringToReturn = preg_replace('/[\-]{2,}/', ' ', $stringToReturn);
	return $stringToReturn;
   } 
    
}
