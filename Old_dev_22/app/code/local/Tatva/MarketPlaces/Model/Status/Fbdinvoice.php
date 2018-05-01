<?php
/**
 * created : 8 oct. 2009
 *
 * @category SQLI
 * @package Tatva_MarketPlaces
 * @author emchaabelasri
 * @copyright SQLI - 2009 - http://www.tatva.com
 *
 * EXIG : REF-005
 * REG  :
 */

/**
 *
 * @package Tatva_MarketPlaces
 */
class Tatva_MarketPlaces_Model_Status_Fbdinvoice extends Mage_Core_Model_Abstract
{
    const STATUS_INVOICE = "processing";

    const GET_ORDER_URL           	   = 'tatvamarketplaces_fbd/get_order/url';
    const GET_ORDER_FTP_Address        = 'tatvamarketplaces_fbd/get_order/ftp_adress';
	const GET_ORDER_login     		   = 'tatvamarketplaces_fbd/get_order/login';
	const GET_ORDER_password           = 'tatvamarketplaces_fbd/get_order/password';
    const GET_ORDER_port               = 'tatvamarketplaces_fbd/get_order/port';

	const SET_ORDER_URL           	   = 'tatvamarketplaces_fbd/set_order/url';
    const SET_ORDER_FTP_Address        = 'tatvamarketplaces_fbd/set_order/ftp_adress';
	const SET_ORDER_login     		   = 'tatvamarketplaces_fbd/set_order/login';
	const SET_ORDER_password           = 'tatvamarketplaces_fbd/set_order/password';
    const SET_ORDER_port               = 'tatvamarketplaces_fbd/set_order/port';

	protected $_fbdModel ;

	protected $_collectionOrders ;

	/**
     * get fbd model
     * @return Tatva_MarketPlaces_Model_Priceminister
	 */
	protected function _fbdModel()
    {
		if( ! $this->_fbdModel )
        {
			$this->_fbdModel = Mage::getModel('tatvamarketplaces/fbd');
		}
		return 	$this->_fbdModel;
	}

	/**
	 * Main function
	 */
	public function execute()
    {
        ini_set ( 'memory_limit', '1024M' );
		Mage::log ( '-----------------', Zend_log::INFO, $this->_fbdModel()->getLogFile() );

		//Enregistrer le fichier
		Mage::log ( 'FBD - Status update at FBD: START', Zend_log::INFO, $this->_fbdModel()->getLogFile() );

		$orderCollection = $this->_getOrderCollection();

		if(!count($orderCollection ) )
        {
			Mage::log ( 'FBD - Status update at FBD: NO Order TO SEND For Invoice', Zend_log::INFO, $this->_fbdModel()->getLogFile() );
			return;
		}

        $ftp_address   = $this->getConfigData(self::GET_ORDER_FTP_Address);
		$login = $this->getConfigData( self:: SET_ORDER_login );
		$pass  = $this->getConfigData( self:: SET_ORDER_password );
        $port   = $this->getConfigData( self:: SET_ORDER_port );

		try
        {
            if (!function_exists("ssh2_connect"))
                die('Function ssh2_connect not found, you cannot use ssh2 here');

            if (!$resConnection = ssh2_connect($ftp_address, $port))
                die('Unable to connect');

            if (!ssh2_auth_password($resConnection, $login, $pass))
                die('Unable to authenticate.');

            if (!$resSFTP = ssh2_sftp($resConnection))
                die('Unable to create a stream.');

            Mage::log ( 'Creation du fichier', Zend_log::INFO, $this->_fbdModel()->getLogFile() );

            foreach($orderCollection as $order)
            {
                $dom = new DOMDocument ( '1.0', 'utf-8' );
        		$dom->formatOutput = true;
        		header ( "Content-Type: text/plain" );

                // Ajout de la balise mmie
        		//$mmie = $dom->createElement ( "mmie" );
        		//$mmie->setAttribute('version','2.0');
        		//$dom->appendChild ( $mmie );

        		// Ajout de la balise orders
        		$orders = $dom->createElement ( "order" );
                /*echo "<pre>";
                    print_r($order->getData());
                    echo "</pre>";
                    echo "<br/><br/>";
                    exit;*/
    		    if( $order->getStatus() == self::STATUS_INVOICE )
                {
                    $Fbd_order = $this->getPartnerOrderRef($order);

                    $header = $dom->createElement ( "header" );
                    $order_fbd_id = $dom->createElement ("order_fbd_id", $Fbd_order);
                    $header->appendChild( $order_fbd_id );
                    $order_supplier_id = $dom->createElement ("order_supplier_id", $order->getIncrementId());
                    $header->appendChild( $order_supplier_id );
                    $supplier_code = $dom->createElement ("supplier_code", "az-boutique");
                    $header->appendChild( $supplier_code );

                    $orders->appendChild($header);

    		  	    Mage::log ( 'CMD N° ='.$order->getIncrementId(), Zend_log::INFO, $this->_fbdModel()->getLogFile() );

                    $items = $dom->createElement ( "items" );

                    /*echo "<pre>";
                    print_r($order->getData());
                    echo "</pre>";
                    echo "<br/><br/>";
                    exit;*/

                    $order_items = $order->getAllItems();

                    foreach($order_items as $order_item)
                    {
                        /*echo "<pre>";
                        print_r($order_item->getData());
                        echo "</pre>";
                        echo "<br/><br/>";*/

                        $item = $dom->createElement ( "item" );

                        $id = $dom->createElement ("id", $order_item->getMarketplacesOrderItemId());
                        $item->appendChild( $id );

                        $fbd_sku = $dom->createElement ("fbd_sku", "az-boutique_".$order_item->getSku());
                        $item->appendChild( $fbd_sku );

                        $supplier_sku = $dom->createElement ("supplier_sku", $order_item->getSku());
                        $item->appendChild( $supplier_sku );

                        $qty = $dom->createElement ("qty", $order_item->getQtyOrdered());
                        $item->appendChild( $qty );

                        $item_qty = $order_item->getQtyInvoiced();
                        $item_ordered = $order_item->getQtyOrdered();

                        $sequences = $dom->createElement ( "sequences" );
                        $ac = 1;
                        for ($x = $ac; $x <= $item_qty; $x++)
                        {
                            $sequence = $dom->createElement ( "sequence" );

                            $seq_id = $dom->createElement ("id", $ac);
                            $sequence->appendChild( $seq_id );

                            $seq_state = $dom->createElement ("state", "accepted");
                            $sequence->appendChild( $seq_state );

                            $sequences->appendChild( $sequence );
                            $ac = $ac + 1;
                        }

                        if($item_qty < $item_ordered)
                        {
                            $canceled_item = $item_ordered - $item_qty;
                            $ax = 1;
                            for ($y = $ax; $y <= $canceled_item; $y++)
                            {
                                $sequence = $dom->createElement ( "sequence" );

                                $seq_id = $dom->createElement ("id", $ac);
                                $sequence->appendChild( $seq_id );

                                $seq_state = $dom->createElement ("state", "canceled");
                                $sequence->appendChild( $seq_state );

                                $sequences->appendChild( $sequence );
                                $ac = $ac + 1;

                            }

                        }

                        $item->appendChild( $sequences );

                        $items->appendChild( $item );
                    }

                    $orders->appendChild( $items );

                    $dom->appendChild($orders);
                    $xml = $dom->saveXML();
                    $xml = $xml."##DONE##";

                    /*echo "<pre>";
                    print_r($xml);
                    echo "</pre>";
                    echo "<br/><br/>";
                    exit;*/

                    $order->setMarketplacesOrderSended ("I");
				    $order->getResource()->saveAttribute ( $order, 'marketplaces_order_sended' );
                    $created_date = date('_Ymd_his');

                    $resFile = fopen("ssh2.sftp://{$resSFTP}/to_fbd/order_update/az-boutique_update_order_".$Fbd_order.$created_date.".xml", 'w');
                    fwrite($resFile,$xml);
    		    }

    		    fclose($resFile);
            }

            /*echo "<pre>";
            echo "sadadad";
            //print_r($xml);
            exit;*/
        }
        catch(Exception $e)
        {
            /*echo "<pre>";
            echo "sadadad";
            print_r($e->getMessage());
            exit;*/
			Mage::log ( $e->getMessage(), Zend_log::ERR, $this->_fbdModel()->getLogFile() );
			Mage::logException ( $e );
		}

		Mage::log ( 'FBD - Status update at FBD: FIN', Zend_log::INFO, $this->_fbdModel()->getLogFile() );
	}

	/**
	 * return order collection for priceminster
	 * @param void
	 * @return order collection
	 */
	protected function _getOrderCollection()
    {
		if(!$this->_collectionOrders)
        {
			$fbdCode = $this->_fbdModel->getCode();
			$this->_collectionOrders = Mage::getModel('sales/order')->getCollection()
							->addAttributeToSelect( '*' )
							->addAttributeToFilter( 'status' , array( 'in'=> array( self::STATUS_INVOICE ) ) )
							->addAttributeToFilter( 'marketplaces_order_sended', array('in'=>'N'))
						    ->addAttributeToFilter( 'marketplaces_partner_code', array('in'=>$fbdCode)) ;
		}

        return $this->_collectionOrders;
	}


	/**
	 * return partner order code
	 * @param Mage_Sales_Model_Order
	 * @return string
	 */
	public function getPartnerOrderRef( $order )
    {
		return $order->getPaymentsCollection()->getFirstItem()->getMarketplacesPartnerOrder();
	}

    /**
     * Retrieve information from configuration
     *
     * @param   string $path
     * @return  mixed
     */
    public function getConfigData($path)
    {
        return Mage::getStoreConfig($path);
    }
}