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
class Tatva_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController
{	
    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     */

	public function getConfigData($field)
	{
        $path = 'carriers/tnt/'.$field;
        return Mage::getStoreConfig($path, Mage::app()->getStore());
	}
	
	public function getConfigData_mondial($field)
	{
        $path = 'carriers/pointsrelais/'.$field;
        return Mage::getStoreConfig($path, Mage::app()->getStore());
	}

	public function dateFR( $dateUS ) {
		$tmp = explode('-',$dateUS);

		$dateFR = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];

		return $dateFR;
	}

    protected function _getItemQtys() {
        $data = $this->getRequest()->getParam('shipment');
        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = array();
        }
        return $qtys;
    }

    /**
     * Initialize shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function _initShipment() {
        $shipment = false;
        if ($shipmentId = $this->getRequest()->getParam('shipment_id')) {
            $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        } elseif ($orderId = $this->getRequest()->getParam('order_id')) {
            $order = Mage::getModel('sales/order')->load($orderId);

            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('Order not longer exist.'));
                return false;
            }
            /**
             * Check shipment create availability
             */
            if (!$order->canShip()) {
                $this->_getSession()->addError($this->__('Can not do shipment for order.'));
                return false;
            }

            $convertor = Mage::getModel('sales/convert_order');
            $shipment = $convertor->toShipment($order);
            $savedQtys = $this->_getItemQtys();
            foreach ($order->getAllItems() as $orderItem) {
                if (!$orderItem->isDummy(true) && !$orderItem->getQtyToShip()) {
                    continue;
                }
                if ($orderItem->isDummy(true) && !$this->_needToAddDummy($orderItem, $savedQtys)) {
                    continue;
                }
                if ($orderItem->getIsVirtual()) {
                    continue;
                }
                $item = $convertor->itemToShipmentItem($orderItem);
                if (isset($savedQtys[$orderItem->getId()])) {
                    if ($savedQtys[$orderItem->getId()] > 0) {
                        $qty = $savedQtys[$orderItem->getId()];
                    } else {
                        continue;
                    }
                } else {
                    if ($orderItem->isDummy(true)) {
                        $qty = 1;
                    } else {
                        $qty = $orderItem->getQtyToShip();
                    }
                }
                $item->setQty($qty);
                $shipment->addItem($item);
            }
            if ($tracks = $this->getRequest()->getPost('tracking')) {
                foreach ($tracks as $data) {
                    $track = Mage::getModel('sales/order_shipment_track')
                                    ->addData($data);
                    $shipment->addTrack($track);
                }
            }
        }

        Mage::register('current_shipment', $shipment);
        return $shipment;
    }

    protected function _saveShipment($shipment) {

        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();

        return $this;
    }

    /**
     * shipment information page
     */
    public function viewAction() {
        if ($shipment = $this->_initShipment()) {
            $this->loadLayout();
            $this->getLayout()->getBlock('sales_shipment_view')
                    ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));
            $this->_setActiveMenu('sales/order')
                    ->renderLayout();
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Start create shipment action
     */
    public function startAction() {
        /**
         * Clear old values for shipment qty's
         */
        $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
    }

    /**
     * Shipment create page
     */
    public function newAction() {
        if ($shipment = $this->_initShipment()) {
            $this->loadLayout()
                    ->_setActiveMenu('sales/order')
                    ->renderLayout();
        } else {
            $this->_redirect('*/sales_order/view', array('order_id' => $this->getRequest()->getParam('order_id')));
        }
    }

    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     */
    public function saveAction() { echo 'nisha';exit;
        $data = $this->getRequest()->getPost('shipment');

        try {
            if ($shipment = $this->_initShipment()) {

                //check if qtys can be shipped
                if (Mage::getStoreConfig('purchase/configuration/check_qty_before_create_shipment') == 1) {
                    $this->checkForProductsQty($shipment);
                }
                $_order = $shipment->getOrder();

				/* shipment flag added by mauli  */
                $cuurent_time=''; $ship_time_by_erp='';
                $cuurent_time=date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));

                $planning = Mage::getModel('SalesOrderPlanning/Planning')->load($shipment['order_id'] , 'psop_order_id');

                $ship_time_by_erp= $planning['psop_shipping_date'];

                 if(strtotime($cuurent_time) <= strtotime($ship_time_by_erp))
                {

                 $shipment->setData('ekomi_flag',1);
                 //echo $shipment->getData('ekomi_flag'); exit;
                }
               /* ended by mauli  */



                $_shippingMethod = explode("_",$_order->getShippingMethod());

                //Expédition via TNT on créé une expé. et on récupère le tracking num via le WS.
                if ($_shippingMethod[0] == 'tnt')  {

                	// On met en place les paramètres de la requète pour l'expédition
                    $send_city = $this->getConfigData('ville');

                    $rec_typeid = '';
                    $rec_name = '';

                    if($_shippingMethod['1'] == "A" || $_shippingMethod['1'] == "T" || $_shippingMethod['1'] == "M" || $_shippingMethod['1'] == "J") {
                    	$rec_type = 'ENTERPRISE';
                    	$rec_name = trim($_order->getShippingAddress()->getCompany());
                    } elseif($_shippingMethod['1'] == "AZ" || $_shippingMethod['1'] == "TZ" || $_shippingMethod['1'] == "MZ" || $_shippingMethod['1'] == "JZ") {
                    	$rec_type = 'INDIVIDUAL';
                    } else {
                    	$rec_type = 'DROPOFFPOINT';
                    	$extt = explode(' ',trim($_order->getShippingAddress()->getCompany()));
                    	$rec_typeid = end($extt);
                    	$rec_name = str_replace($rec_typeid, '', $_order->getShippingAddress()->getCompany());
                    }

                    $rec_address1 = $_order->getShippingAddress()->getStreet(1);
                    $rec_address2 = $_order->getShippingAddress()->getStreet(2);

                	if ( $rec_address2 == '' ) {
                        if( strlen($rec_address1) > 32 ) {
                        	$rec_address2 = substr($rec_address1,32,64);
                        }
                    }

                    $nb_colis = $this->getRequest()->getPost('nb_colis');
                    $date_expe = $this->dateFR( $this->getRequest()->getPost('shippingDate') );

                    $parcelsRequest = array();

                    $poids_restant = $_order->getWeight();
                    for($i=1;$i<=$nb_colis;$i++) {
                    	if($i == $nb_colis) {
                    		if( $poids_restant > $this->getConfigData('max_package_weight') ) {
                    			$parcelWeight = $this->getConfigData('max_package_weight');
                    		} else {
                    			$parcelWeight = $poids_restant;
                    		}
                    	} else {
                    		$parcelWeight = $this->getConfigData('max_package_weight');
                    		$poids_restant = $poids_restant - $parcelWeight;
                    	}

                    	if($parcelWeight < '0.1') {
                    		$parcelWeight = '0.1';
                    	}

                    	$parcelsRequest[] = array('sequenceNumber'=>$i,'customerReference' => $_order->getRealOrderId(), 'weight' => $parcelWeight);
                    }

                    $rec_city = $_order->getShippingAddress()->getCity();
                    
                    $instructions = '';
                    $phoneNumber = '';
                    $accessCode = '';
                    $floorNumber = '';
                    $buildingId = '';
                    
                    $info_comp = explode('&&&', $_order->getShippingAddress()->getTntInfosComp());
                    
                    if( count($info_comp) > 0 ) {
                    	if( count($info_comp) == 1 ) {
                    		$instructions = substr($info_comp[0], 0, 60); 
                    	} else {
                    		$phoneNumber = $info_comp[0];
                    		$accessCode = $info_comp[1];
                    		$floorNumber = $info_comp[2];
                    		$buildingId = $info_comp[3];
                    	}
                    }                    
                    if($phoneNumber == '') {
                    	$phoneNumber = $_order->getShippingAddress()->getTelephone();
                    }
                    
                    $phoneNumber = str_replace(' ', '', $phoneNumber);
                    if( preg_match('/^0033/', $phoneNumber) ) {
                    	$phoneNumber = substr_replace($phoneNumber, '0', 0, 4);
                    }
                    $phoneNumber = str_replace('+33', '0', $phoneNumber);
                    $phoneNumber = str_replace('(+33)', '0', $phoneNumber);
                    $phoneNumber = str_replace('-', '', $phoneNumber);
                    $phoneNumber = str_replace('.', '', $phoneNumber);
                    $phoneNumber = str_replace(',', '', $phoneNumber);
                    $phoneNumber = str_replace('/', '', $phoneNumber);

                    $sender = array('zipCode' => $this->getConfigData('code_postal'), 'city' => $send_city);
                    $receiver = array('zipCode' => $_order->getShippingAddress()->getPostcode(), 'city' => $rec_city, 'type' => $rec_type);
                    $feasi_params = array('shippingDate' => $date_expe, 'accountNumber' => $this->getConfigData('account'), 'sender' => $sender, 'receiver' => $receiver );
                    $feasi_result = Mage::getModel('tnt/shipping_carrier_tnt')->_tnt_feasibility( $feasi_params );

                    if( is_string($feasi_result) ) {
                    	Throw new Exception( $feasi_result );
                    }
                    
                    //correction du bug Paypal qui concatene nom/prenom et vide le nom de l'adresse de facturation !!
                    if( trim($_order->getShippingAddress()->getLastname()) == '' && trim($_order->getShippingAddress()->getFirstname()) != '' ) {
                    	$nom = '';
                    	$prenom = $_order->getShippingAddress()->getFirstname();
                    	$tab_nom = explode(" ", $prenom);
                    	
                    	for( $i=0;$i<count($tab_nom);$i++ ) {
                    		if( $i == 0 ) {
                    			$prenom = substr($tab_nom[$i],0,12);
                    		} else {
                    			$nom.= $tab_nom[$i]." ";
                    		}
                    	}
                    	
                    	$nom = trim($nom);
                    	$nom = substr($nom,0,19);
                    	
                    } else {
                    	$nom = substr($_order->getShippingAddress()->getLastname(),0,19);
                    	$prenom = substr($_order->getShippingAddress()->getFirstname(),0,12);
                    }

                    $params = array('parameters' => array( 	'shippingDate'   => $date_expe,
                    										'accountNumber'  => $this->getConfigData('account'),
                    										'sender' 	 	 => array(	'name' => substr($this->getConfigData('raison_sociale'),0,32),
						                    											'address1'   => substr($this->getConfigData('adresse'),0,32),
						                    											'address2'   => substr($this->getConfigData('adresse2'),0,32),
						                    											'zipCode'    => substr($this->getConfigData('code_postal'),0,5),
						                    											'city'       => substr($send_city,0,27)
						                    											),
															'receiver'     	=> array(	'type' => $rec_type,
						                    											'typeId' => $rec_typeid,
																						'name' => substr($rec_name,0,32),
						                    											'address1' => substr($rec_address1,0,32),
						                    											'address2' => substr($rec_address2,0,32),
						                    											'zipCode' => substr($_order->getShippingAddress()->getPostcode(),0,5),
						                    											'city' => substr($rec_city,0,27),
						                    											'instructions' => $instructions,
						                    											'contactLastName' => $nom,
						                    											'contactFirstName' => $prenom,
						                    											'emailAddress' => substr($_order->getCustomerEmail(),0,80),
						                    											'phoneNumber' => substr($phoneNumber,0,10),
						                    											'accessCode' => substr($accessCode,0,7),
						                    											'floorNumber' => substr($floorNumber,0,2),
						                    											'buldingId' => substr($buildingId,0,3)
						                    											),
						                                   'serviceCode'   	=> $_shippingMethod[1],
						                                   'quantity'       => $nb_colis,
						                                   'parcelsRequest' => $parcelsRequest,
						                    			   'labelFormat'	=> $this->getConfigData('label_format')
						                    				)
								);
                    
					$parcels = Mage::getModel('tnt/shipping_carrier_tnt')->_tnt_exp_crea($params);
					
                	if( is_string($parcels) ) {
echo 'hey==';print_r($this->__($parcels));exit;
                    	Throw new Exception( $this->__($parcels) );
                	}
                	
                	//on créé le fichier PDF
                    $path = Mage::getBaseDir('media').'/pdf_bt/';
                    $filename = $_order->getRealOrderId().".pdf";

                    if($parcels['pdfLabels'] && !file_exists($path.$filename)) {
                    	if( $handle = fopen($path.$filename, 'x+') ) {
	                    	fwrite($handle, $parcels['pdfLabels']);
	                    	fclose($handle);
                    	} else {
                    		Throw new Exception( $this->__("Impossible de créer le BT. Vérifiez que le repertoire /media/pdf_bt/ à les droits en écriture.") );
                    	}
                    }
                    foreach($parcels as $parcel) {
                    	if(is_array($parcel)) {
		                    $track = Mage::getModel('sales/order_shipment_track')
		                        ->setNumber($parcel['parcelNumber'])
		                        ->setCarrier('TNT')
		                        ->setCarrierCode($_shippingMethod[0])
		                        ->setTitle('TNT')
		                        ->setPopup(1);
		                    $shipment->addTrack($track);
                    	}
                    }
                }
			   
				 

                if ($_shippingMethod[0] == 'pointsrelais')  {

                    // On met en place les paramÃ¨tres de la requÃ¨te
                    
                    $adress = $_order->getShippingAddress()->getStreet();

                    //echo '<pre>';print_r($_order->getShippingAddress()->getData());exit;
                    if (!isset($adress[1]))
                    {
                        $adress[1] = '';
                    }
                    $package_weightTmp = $_order->getWeight()*1000;

			        if($this->getConfigData_mondial('package_weight')){
			        	$package_weightTmp = $package_weightTmp+($this->getConfigData_mondial('package_weight'));
			        }

                    if($package_weightTmp < 100){
                    	$package_weightTmp = 100;
                    }
                    /*echo '<pre>';
                    print_r($_order->getShippingAddress()->getData());exit;*/
                    $params = array(
                                   'Enseigne'       => $this->getConfigData_mondial('enseigne'),
                                   'ModeCol'        => 'CCC',
                                   'ModeLiv'        => '24R',
                                   'Expe_Langage'   => 'FR',
                                   'Expe_Ad1'       => trim($this->removeaccents($this->getConfigData_mondial('adresse1_enseigne'))),
                                   'Expe_Ad3'       => trim($this->removeaccents($this->getConfigData_mondial('adresse3_enseigne'))),
                                   'Expe_Ad4'       => trim($this->removeaccents($this->getConfigData_mondial('adresse4_enseigne'))),
                                   'Expe_Ville'     => trim($this->removeaccents($this->getConfigData_mondial('ville_enseigne'))),
                                   'Expe_CP'        => $this->getConfigData_mondial('cp_enseigne'),
								   'Expe_Pays'      => trim($this->removeaccents($this->getConfigData_mondial('pays_enseigne'))),
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
                                   'Poids'          => round($package_weightTmp),
                                   'NbColis'        => '1',
                                   'CRT_Valeur'     => '0',
                                   'LIV_Rel_Pays'   => $_order->getShippingAddress()->getCountryId(),
                                   'LIV_Rel'        => $_order->getRelayId()
                    );//$_order->getWeight()*1000,
                    //On crÃ©e le code de sÃ©curitÃ©

                    $select = "";
                    foreach($params as $key => $value){
					    $select .= "\t".'<option value="'.$key.'">' . $value.'</option>'."\r\n";
					}
                    Mage::Log('WSI2_CreationExpeditionResult$params : '.($select));

                    $code = implode("",$params);
                    $code .= $this->getConfigData_mondial('cle');
                    
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
                }
				$shipment->register();
				//GLS
				
				if($_shippingMethod[0] == 'gls')  
				 { 
					$shipfromID = 1;
                    
					$shipfrom = $this->_initShipfrom($shipfromID);
				   //	$weight = $_order->getWeight();
				   	$weight = $_order->getWeight();
					$notiz = $this->getRequest()->getPost('notiz');
		            $extra['frankatur'] = $this->getRequest()->getPost('frankatur');
		            $extra['expressart'] = $this->getRequest()->getPost('expressart');
		            $extra['alternativzustellung'] = $this->getRequest()->getPost('alternativzustellung');
		            $extra['paketsum'] = $this->getRequest()->getPost('paketsum');
		            $extra['paketnumber'] = $this->getRequest()->getPost('paketnumber');
		            $extra['notiz'] = $notiz;
					
					//echo 'yes=='.$weight;exit;
					$response_gls = $this->_initGlsService('business',$shipment,$weight,$shipfrom,$notiz,$extra);
				   //echo 'tes=='.print_r($response_gls);exit;
		                if (is_String($response_gls)) {
		                    $response = array(
		                        'error'     => true,
		                        'message'   => $response_gls,
		                    );
		                } else {
		                    
		                    $this->_insertTracking($shipment,$response_gls,$notiz);
		                }
			   }
                
                

                
				
				
				
				//for gls - start
			
			/*$order = Mage::getModel('sales/order')->load($shipment->getOrderId()); */
			
			//for gls - end

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
                $this->_redirect('*/sales_order/view', array('order_id' => $shipment->getOrderId()));
                return;
            } else {
                $this->_forward('noRoute');
                return;
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {echo $e->getMessage();exit;
            $this->_getSession()->addError($this->__('Can not save shipment.'));
        }
        $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
    }

    public function emailAction() {
        try {
            if ($shipment = $this->_initShipment()) {
                $shipment->sendEmail(true)
                        ->setEmailSent(true)
                        ->save();
                $this->_getSession()->addSuccess($this->__('Shipment was successfully sent.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Can not send shipment information.'));
        }
        $this->_redirect('*/*/view', array(
            'shipment_id' => $this->getRequest()->getParam('shipment_id')
        ));
    }

    /**
     * Add new tracking number action
     */
    public function addTrackAction() {
        try {
            $carrier = $this->getRequest()->getPost('carrier');
            $number = $this->getRequest()->getPost('number');
            $title = $this->getRequest()->getPost('title');
            if (empty($carrier)) {
                Mage::throwException($this->__('You need specify carrier.'));
            }
            if (empty($number)) {
                Mage::throwException($this->__('Tracking number can not be empty.'));
            }
            if ($shipment = $this->_initShipment()) {
                $track = Mage::getModel('sales/order_shipment_track')
                                ->setNumber($number)
                                ->setCarrierCode($carrier)
                                ->setTitle($title);
                $shipment->addTrack($track)
                        ->save();

                $this->loadLayout();
                $response = $this->getLayout()->getBlock('shipment_tracking')->toHtml();
            } else {
                $response = array(
                    'error' => true,
                    'message' => $this->__('Can not initialize shipment for adding tracking number.'),
                );
            }
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error' => true,
                'message' => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error' => true,
                'message' => $this->__('Can not add tracking number.'),
            );
        }
        if (is_array($response)) {
            $response = Zend_Json::encode($response);
        }
        $this->getResponse()->setBody($response);
    }

    public function removeTrackAction() {
        $trackId = $this->getRequest()->getParam('track_id');
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $track = Mage::getModel('sales/order_shipment_track')->load($trackId);
        if ($track->getId()) {
            try {
                if ($shipmentId = $this->_initShipment()) {
                    $track->delete();

                    $this->loadLayout();
                    $response = $this->getLayout()->getBlock('shipment_tracking')->toHtml();
                } else {
                    $response = array(
                        'error' => true,
                        'message' => $this->__('Can not initialize shipment for delete tracking number.'),
                    );
                }
            } catch (Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $this->__('Can not delete tracking number.'),
                );
            }
        } else {
            $response = array(
                'error' => true,
                'message' => $this->__('Can not load track with retrieving identifier.'),
            );
        }
        if (is_array($response)) {
            $response = Zend_Json::encode($response);
        }
        $this->getResponse()->setBody($response);
    }

    public function viewTrackAction() {
        $trackId = $this->getRequest()->getParam('track_id');
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $track = Mage::getModel('sales/order_shipment_track')->load($trackId);
        if ($track->getId()) {
            try {
                $response = $track->getNumberDetail();
            } catch (Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $this->__('Can not retrieve tracking number detail.'),
                );
            }
        } else {
            $response = array(
                'error' => true,
                'message' => $this->__('Can not load track with retrieving identifier.'),
            );
        }

        if (is_object($response)) {
            $className = Mage::getConfig()->getBlockClassName('adminhtml/template');
            $block = new $className();
            $block->setType('adminhtml/template')
                    ->setIsAnonymous(true)
                    ->setTemplate('sales/order/shipment/tracking/info.phtml');

            $block->setTrackingInfo($response);

            $this->getResponse()->setBody($block->toHtml());
        } else {
            if (is_array($response)) {
                $response = Zend_Json::encode($response);
            }

            $this->getResponse()->setBody($response);
        }
    }

    public function addCommentAction() {
        try {
            $this->getRequest()->setParam(
                    'shipment_id',
                    $this->getRequest()->getParam('id')
            );
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('Comment text field can not be empty.'));
            }
            $shipment = $this->_initShipment();
            $shipment->addComment($data['comment'], isset($data['is_customer_notified']));
            $shipment->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
            $shipment->save();

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('shipment_comments')->toHtml();
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error' => true,
                'message' => $e->getMessage()
            );
            $response = Zend_Json::encode($response);
        } catch (Exception $e) {
            $response = array(
                'error' => true,
                'message' => $this->__('Can not add new comment.')
            );
            $response = Zend_Json::encode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Decides if we need to create dummy shipment item or not
     * for eaxample we don't need create dummy parent if all
     * children are not in process
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $qtys
     * @return bool
     */
    protected function _needToAddDummy($item, $qtys) {
        if ($item->getHasChildren()) {
            foreach ($item->getChildrenItems() as $child) {
                if ($child->getIsVirtual()) {
                    continue;
                }
                if ((isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) || (!isset($qtys[$child->getId()]) && $child->getQtyToShip())) {
                    return true;
                }
            }
            return false;
        } else if ($item->getParentItem()) {
            if ($item->getIsVirtual()) {
                return false;
            }
            if ((isset($qtys[$item->getParentItem()->getId()]) && $qtys[$item->getParentItem()->getId()] > 0)
                    || (!isset($qtys[$item->getParentItem()->getId()]) && $item->getParentItem()->getQtyToShip())) {
                return true;
            }
            return false;
        }
    }

    /**
     * Check if qty to ship are in stock
     *
     */
    public function checkForProductsQty($shipment) {
        foreach ($shipment->getAllItems() as $item) {
            $qty = $shipment->getRealShippedQtyForItem($item);
            $productId = $item->getproduct_id();
            $preparationWarehouseId = $item->getOrderItem()->getpreparation_warehouse();
            $stockItem = mage::getModel('cataloginventory/stock_item')->loadByProductWarehouse($productId, $preparationWarehouseId);
            if ($stockItem)
            {
                if ($stockItem->getManageStock() && ($qty > 0)) {
                    if ($qty > $stockItem->getQty()) {
                        throw new Mage_Core_Exception($item->getname() . ' : ' . mage::helper('purchase')->__(' stock level too low.'));
                    }
                }
            }
        }
    }
	
	private function _initShipfrom($id)
    {
        $client = false;
        if ($id) {
            $client = Mage::getModel('glsbox/client')->getCollection()->addFieldToFilter('status', '1')->addFieldToFilter('id', $id)->getFirstItem();
        }
        return $client;
    }
	
	protected function _initGlsService($service,$shipment,$weight,$shipfrom,$notiz,$extra){
        $submit = Mage::getModel('glsbox/unibox_parser');
        $gls = $submit->create($service,$shipment,$weight,$shipfrom,$notiz,$extra);
        return $gls;
    }
	
	protected function _insertTracking($shipment,$gls_return,$notiz){
        if (Mage::helper('glsbox')->getAutoInsertTracking() == true) {
            try{
                $paketnummer = $gls_return->getItemsByColumnValue('tag', '8916');
                $paketnummer = $paketnummer[0]->getValue();
				
				$paketnummer_1 = $gls_return->getItemsByColumnValue('tag', '8913');
				$paketnummer_1 = $paketnummer_1[0]->getValue();
					
                if ($notiz == "") { $notiz = 'GLS'; }
                $arrTracking = array(
                    'carrier_code' => 'gls',
                    'title' => $notiz,
                    'number' => $paketnummer_1
                );
				$shipment->save();
                $track = Mage::getModel('sales/order_shipment_track')->addData($arrTracking);
                $shipment->addTrack($track)->save();
				
				$gls_data = Mage::getModel('glsbox/shipment')->getCollection()->getLastItem();
				
				$gls_model = Mage::getModel('glsbox/shipment')->load($gls_data->getId());  
				$gls_model->setShipmentId($shipment->getId());
				$gls_model->save();
				//echo $gls_data->getId();exit;
				
            }catch (Exception $e){echo $e->getMessage();exit;
                Mage::throwException($this->__('Es gibt Probleme mit der Unibox-Kommunikation.'));
            }
        }
    }
	
	function removeaccents($string){ 
	   $stringToReturn = str_replace( 
	   array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý','/','\xa8'), 
	   array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y',' ','e'), $string);
	   // Remove all remaining other unknown characters
	$stringToReturn = preg_replace('/[^a-zA-Z0-9\-]/', ' ', $stringToReturn);
	$stringToReturn = preg_replace('/^[\-]+/', '', $stringToReturn);
	$stringToReturn = preg_replace('/[\-]+$/', '', $stringToReturn);
	$stringToReturn = preg_replace('/[\-]{2,}/', ' ', $stringToReturn);
	return $stringToReturn;
   } 

}