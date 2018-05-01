<?php
/**
 * created : 6 oct. 2009
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
class Tatva_MarketPlaces_Model_Status_Priceminister extends Mage_Core_Model_Abstract{

	const STATUS_CANCELED = "canceled";
	const STATUS_COMPLETE = "complete";
	const DELIMITEUR      = "|";
                    	
	protected $_priceministerModel ; 
	
	/**
     * get priceminister model 
     * @return Tatva_MarketPlaces_Model_Priceminister 
	 */
	protected function _priceministerModel(){
		if( ! $this->_priceministerModel ){
			$this->_priceministerModel = Mage::getModel('tatvamarketplaces/priceminister');
		}
		return 	$this->_priceministerModel;
	}
	
	/**
     * get FTP connexion 
     * @return Varien_Io_Ftp 
	 */
	protected function getFtpConnection(){
		return $this->_priceministerModel()->getFtpConnextion();
	}
	
	/**
     * Execution
	 */
	public function execute(){
		$orderCollection = $this->_getOrderCollection();
		if( ! count ( $orderCollection ) ){
			Mage::log ( 'PriceMinister - Mis à jours des status chez Priceminister: AUCUNE CMD A ENVOYER', Zend_log::INFO, $this->_priceministerModel()->getLogFile() );
		}

		$csvLines = "";
		//- Parcours les commandes expédiées ou annullées
		foreach ( $orderCollection as $order  ){
			// initialisation des variables
			$sellerAccountId     = "" ;  // KO TODO           
			$pmPurchaseId        = "" ;	 // OK			   
			$pmItemId            = "" ;  // OK
			$sellerAdvertRef     = "" ;  // KO TODO
			$authorisationDate   = "" ;  // OK
			$validationOperation = "" ;  // OK
			
			// Date
			$authorisationDate = $order->getCreatedAt();
			
			// Satatus
			if( $order->getStatus() == self::STATUS_CANCELED ){
				$validationOperation = "CANCEL";
			}
			if( $order->getStatus() == self::STATUS_COMPLETE ){
				$validationOperation = "CONFIRM";
			}			
			
			// Purshase Id 
			$pmPurchaseId = $order->getPaymentsCollection()->getFirstItem()->getMarketplacesPartnerOrder() ;
			
			// Ajout d'une ligne par Item
			foreach (   $order->getItemsCollection() as $item  ) {
				$pmItemId = $item->getProductId();
				
				// former une ligne 
				$csvLines .= $sellerAccountId .self::DELIMITEUR. 
							 $pmPurchaseId .self::DELIMITEUR. 
							 $pmItemId .self::DELIMITEUR. 
							 $sellerAdvertRef .self::DELIMITEUR. 
							 $authorisationDate .self::DELIMITEUR. 
							 $validationOperation ."\n";
			}
			
			//Passe la valeur de marketplaces_order_sended à O
			$order->setMarketplacesOrderSended ( "O" );
			$order->getResource ()->saveAttribute ( $order, 'marketplaces_order_sended' );			
			
		} 	
		try {		
		//- Accès au répertoire "validation"
		$this->getFtpConnection()->cd('validation');
		
		//- Ecriture du fichier item_validation ...
		$this->getFtpConnection()->write( $this->getItemValidationFileName() , $csvLines );
		}catch(Exception $e){
    		throw $e;
    	}
			
	}
	
	/**
	 * return order collection for priceminster
	 * @param void
	 * @return order collection
	 */
	protected function _getOrderCollection(){
		$priceministerCode = Mage::getModel('tatvamarketplaces/priceminister')->getCode();
		$collection = Mage::getModel('sales/order')->getCollection()
						->addAttributeToSelect( '*' )
						->addAttributeToFilter( 'marketplaces_order_sended', array('nin'=>'O'))
						->addAttributeToFilter( 'status' , array( 'in'=> array( self::STATUS_CANCELED , self::STATUS_COMPLETE  ) ) )
					    ->addAttributeToFilter( 'marketplaces_partner_code', array('in'=>$priceministerCode))  	;
		return $collection;				  	
	}
	
	/**
	 * retur item_validation file name
	 * @return string
	 */
	public function getItemValidationFileName(){
		return 'item_validation_'.date('Y_m_d_H').'.csv';
	}
	
	
}
