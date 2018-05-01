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
class Tatva_MarketPlaces_Model_Status_Fbdshipment extends Mage_Core_Model_Abstract
{
    const STATUS_CANCELED = "canceled";
	const STATUS_COMPLETE = "complete";
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
			Mage::log ( 'FBD - Status update at FBD: NO Order TO SEND For Shipping', Zend_log::INFO, $this->_fbdModel()->getLogFile() );
			return;
		}

        $ftp_address   = $this->getConfigData(self::GET_ORDER_FTP_Address);
		$login = $this->getConfigData( self:: SET_ORDER_login );
		$pass  = $this->getConfigData( self:: SET_ORDER_password );
        $port   = $this->getConfigData( self:: SET_ORDER_port );

		try
        {
            //if (!function_exists("ssh2_connect"))
                //die('Function ssh2_connect not found, you cannot use ssh2 here');

            //@mail("sagarshahitprofessional@gmail.com","FBD order update","2.shipment order checked");
            if (!$resConnection = ssh2_connect($ftp_address, $port))
                die('Unable to connect');

            if (!ssh2_auth_password($resConnection, $login, $pass))
                die('Unable to authenticate.');

            if (!$resSFTP = ssh2_sftp($resConnection))
                die('Unable to create a stream.');

            Mage::log ( 'Creation du fichier', Zend_log::INFO, $this->_fbdModel()->getLogFile() );

            $resource = Mage::getSingleton('core/resource');

            $readConnection = $resource->getConnection('core_read');

            foreach($orderCollection as $order)
            {
                $dom = new DOMDocument ( '1.0', 'utf-8' );
        		$dom->formatOutput = true;
        		header ( "Content-Type: text/plain" );

        		// Ajout de la balise orders
        		$orders = $dom->createElement ( "order" );


    		    if( $order->getStatus() == self::STATUS_COMPLETE || $order->getStatus() == self::STATUS_INVOICE )
                {
                    //@mail("sagarshahitprofessional@gmail.com","FBD Shippment","1.".$order->getIncrementId()."Order update found");
                    $Fbd_order = $this->getPartnerOrderRef($order);

                    // Create Basic header info with order details
                    $header = $dom->createElement ( "header" );
                    $order_fbd_id = $dom->createElement ("order_fbd_id", $Fbd_order);
                    $header->appendChild( $order_fbd_id );
                    $order_supplier_id = $dom->createElement ("order_supplier_id", $order->getIncrementId());
                    $header->appendChild( $order_supplier_id );
                    $supplier_code = $dom->createElement ("supplier_code", "az-boutique");
                    $header->appendChild( $supplier_code );

                    $orders->appendChild($header);

    		  	    Mage::log ( 'CMD N° ='.$order->getIncrementId(), Zend_log::INFO, $this->_fbdModel()->getLogFile() );

                    // Create Basic ordern item info with its invoice details
                    $items = $dom->createElement ( "items" );

                    /*echo "<pre>";
                    print_r($order->getData());
                    echo "</pre>";
                    echo "<br/><br/>";*/

                    $order_items = $order->getAllItems();

                    $check_result = "Y";

                    foreach($order_items as $order_item)
                    {
                        /*echo "<pre>";
                        print_r($order_item->getData());
                        echo "</pre>";
                        echo "<br/><br/>";*/

                        $item = $dom->createElement ( "item" );

                        // Append Ordered Product Id
                        $id = $dom->createElement ("id", $order_item->getMarketplacesOrderItemId());
                        $item->appendChild( $id );

                        // Append Ordered Product Sku
                        $fbd_sku = $dom->createElement ("fbd_sku", "az-boutique_".$order_item->getSku());
                        $item->appendChild( $fbd_sku );

                        // Append Ordered Product SKu
                        $supplier_sku = $dom->createElement ("supplier_sku", $order_item->getSku());
                        $item->appendChild( $supplier_sku );

                        // Append Ordered Product Shipped Qty
                        $qty = $dom->createElement ("qty", $order_item->getQtyOrdered());
                        $item->appendChild( $qty );

                        $item_qty = $order_item->getQtyShipped();

                        $sequences = $dom->createElement ("sequences");

                        $shipment_query = "SELECT
                                                shipment_item.order_item_id,
                                                shipment_item.qty as shipment_qty,
                                                shipment_track.track_number,
                                                shipment_track.description,
                                                shipment_track.updated_at as shipment_updated_at,
                                                shipment_grid.increment_id as shipment_inc_id,
                                                invoice.increment_id as invoice_inc_id,
                                                invoice.updated_at as invoice_updated_at,
                                                invoice.grand_total as invoice_grand_total,
                                                invoice_item.qty as invoice_qty,
                                                invoice.pdf_file as invoice_pdf_file,
                                                invoice.store_id as invoice_store_id
                                            FROM sales_flat_shipment_item as shipment_item
                                            INNER JOIN
                                                sales_flat_shipment_track as shipment_track
                                                ON shipment_item.parent_id=shipment_track.parent_id
                                            INNER JOIN
                                                sales_flat_shipment_grid as shipment_grid
                                                ON shipment_track.parent_id=shipment_grid.entity_id
                                            INNER JOIN
                                                sales_flat_invoice as invoice
                                                ON shipment_grid.order_id=invoice.order_id
                                            INNER JOIN
                                                sales_flat_invoice_item as invoice_item
                                                ON invoice.entity_id=invoice_item.parent_id
                                                and shipment_item.order_item_id=".$order_item->getItemId()."
                                                and invoice_item.order_item_id=".$order_item->getItemId();

                        $shipment_results = $readConnection->fetchRow($shipment_query);

                        /*echo "<pre>";
                        print_r($shipment_results);
                        echo "</pre>";
                        echo "<br/><br/>";*/
                        //exit;

                        if(!empty($shipment_results))
                        {
                            $check_result = "N";
                            $ac = 1;
                            for ($x = $ac; $x <= $shipment_results['shipment_qty']; $x++)
                            {
                               $sequence = $dom->createElement ( "sequence" );

                                // Append Incremented Id
                                $seq_id = $dom->createElement ("id", $ac);
                                $sequence->appendChild( $seq_id );

                                // Append Ordered Product Status
                                $seq_state = $dom->createElement ("state", "shipped");
                                $sequence->appendChild( $seq_state );


                                $shipment = $dom->createElement ( "shipment" );

                                $sequence->appendChild( $shipment );
                                // Append Shipment Id
                                $shipment_id = $dom->createElement ("id", $shipment_results['shipment_inc_id']);
                                $shipment->appendChild( $shipment_id );

                                // Append Shipment created at
                                $shipment_created_at = $dom->createElement ("shipped_at", $shipment_results['shipment_updated_at']);
                                $shipment->appendChild($shipment_created_at);

                                // Append Shipment tracking
                                $tracking_number = $dom->createElement ("tracking_number", $shipment_results['track_number']);
                                $shipment->appendChild( $tracking_number );

                                // Append Shipment tracking
                                $tracking_url = $dom->createElement ("tracking_url", $shipment_results['description']);
                                $shipment->appendChild( $tracking_url );

                                $sequence->appendChild( $shipment );

                                $invoice = $dom->createElement ( "invoice" );

                                // Append Invoice Id
                                $invoice_id = $dom->createElement ("id", $shipment_results['invoice_inc_id']);
                                $invoice->appendChild($invoice_id);

                                // Append Invoice created at
                                $invoiced_at = $dom->createElement ("invoiced_at", $shipment_results['invoice_updated_at']);
                                $invoice->appendChild($invoiced_at);

                                // Append Invoice amount
                                $invoice_grand_total = $dom->createElement ("amount", $shipment_results['invoice_grand_total']);
                                $invoice->appendChild($invoice_grand_total);

                                // Append Invoice pdf_filename
                                $pdf_filename = $dom->createElement ("pdf_filename", $shipment_results['invoice_inc_id'].".pdf");
                                $invoice->appendChild( $pdf_filename );

                                //echo $root.$shipment_results['invoice_pdf_file'];
                                //echo "<br />";
                                //echo '/to_fbd/order_invoice/'.$shipment_results['invoice_inc_id'].".pdf";
                                //echo "<br />";
                                //echo "<br />";
                                $root = Mage::getStoreConfig('sales/pdf/path_invoice', $shipment_results['invoice_store_id']);
                                //ssh2_scp_send($resConnection, $root.$shipment_results['invoice_pdf_file'], '/home/main/supplier/az-boutique/to_fbd/order_invoice/', 0644);
                                $localFile=$root.$shipment_results['invoice_pdf_file'];
                                $remoteFile=$shipment_results['invoice_inc_id'].".pdf";
                                $stream = fopen("ssh2.sftp://{$resSFTP}/to_fbd/order_invoice/".$remoteFile, 'w');
                                $file = file_get_contents($localFile);
                                fwrite($stream, $file);
                                fclose($stream);


                                $sequence->appendChild( $invoice );

                                $sequences->appendChild( $sequence );
                                $ac = $ac + 1;
                            }

                            $item_ordered = $order_item->getQtyOrdered();
                            //echo "<br />";
                            $item_shipped = $shipment_results['shipment_qty'];
                            //echo "<br />";
                            //echo "<br />";

                            /*if($item_shipped < $item_ordered)
                                $comapre_item = "N";*/

                            if($item_shipped < $item_ordered)
                            {
                                $canceled_item = $item_ordered - $item_shipped;
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
                        }
                        else
                        {
                           continue;
                        }

                        $item->appendChild( $sequences );

                        // Append Each Item
                        $items->appendChild( $item );
                    }

                    if($check_result == "N")
                    {
                        // Append Items
                        $orders->appendChild( $items );

                        $dom->appendChild($orders);
                        $xml = $dom->saveXML();
                        $xml = $xml."##DONE##";

                        /*echo "<pre>";
                        print_r($xml);
                        echo "</pre>";
                        echo "<br/><br/>";*/
                        //exit;

                     //@mail("sagarshahitprofessional@gmail.com","FBD Shippment","2.".$order->getIncrementId()."-".$Fbd_order."Order update Completed");
                        $order->setMarketplacesOrderSended ("O");

    				    $order->getResource()->saveAttribute ( $order, 'marketplaces_order_sended' );
                        $created_date = date('_Ymd_his');

                        $resFile = fopen("ssh2.sftp://{$resSFTP}/to_fbd/order_update/az-boutique_update_order_".$Fbd_order.$created_date.".xml", 'w');
                        fwrite($resFile,$xml);


                    }
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
							->addAttributeToFilter( 'status' , array( 'in'=> array(self::STATUS_CANCELED , self::STATUS_COMPLETE , self::STATUS_INVOICE  ) ) )
							->addAttributeToFilter( 'marketplaces_order_sended', array('in'=>'I'))
						    ->addAttributeToFilter( 'marketplaces_partner_code', array('in'=>$fbdCode)) ;
		}
            /*echo $this->_collectionOrders->getSelect();
            exit;*/
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