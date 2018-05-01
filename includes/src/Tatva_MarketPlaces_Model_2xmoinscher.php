<?php
/**
 * created : 30 sept. 2009
 *
 * 
 * updated by <user> : <date>
 * Description of the update
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
set_include_path(get_include_path().PS.Mage::getBaseDir('lib').DS.'Spreadsheet'.DS.'Excel');
require_once 'reader.php';

class Tatva_MarketPlaces_Model_2xmoinscher extends Tatva_MarketPlaces_Model_Abstract
{
	protected $_code = '2xmoinscher';
	protected $_pathXmlShippingMethod = 'tatvamarketplaces_2xmoinscher/shipping_methods/mapping';
	protected $_pathXmlEnabled = 'tatvamarketplaces_2xmoinscher/orders/active';
	
	protected $_filename;
	protected $_path;
	protected $_fileXls;
	protected $_files;
	
	const C_DATE 			= 1; 
	const C_ID_COMMANDE 	= 2;
	const C_MODE_ENVOI 		= 5;
	const C_FRAIS_PORT 		= 17;
	const C_ACHETEUR_NOM 	= 6;
	const C_ACHETEUR_RUE 	= 7;
	const C_ACHETEUR_CP 	= 8;
	const C_ACHETEUR_VILLE	= 9;
	const C_ACHETEUR_PAYS 	= 10;
	const C_ACHETEUR_TEL 	= 16;
	const C_PRIX_VENTE 		= 11;
	const C_SKU 			= 15;
	
	/**
	 * Initialisation
	 */
	protected function init(){
		try{
			$this->_initPath();		
			$this->_initFilename();

		}catch(Exception $e){
			throw $e;
		}
	}
	
	/**
	 * Active / Désactive
	 */
	protected function isEnabled(){
		try{
			return Mage::getStoreConfigFlag ( $this->_pathXmlEnabled );

		}catch(Exception $e){
			throw $e;
		}
	}
	
	/**
	 * Exécution
	 */
	protected function _execute(){
		
		foreach ( $this->_files as $file ) {
			$this->setSourceFile ( $file);
			$this->_prepareReadXls();	
				
			//Initialisation des valeurs utilisées pour les commandes
			$oldOrder = "";
			$dateOrder = "";
			$shippingMethod = "";
			$totalTTC = 0;
			$totalTaxAmount = 0;
			$shippingAmount = 0;
					
			//Initialisation des valeurs utilisées pour les produits
			$oldProduct = "";
			$qty = 0;
			$totalLigneTaxAmount = 0;
			$totalLigneTTC = 0;
			$priceHT = 0;
			
			$taxPercent = false;
			$error = false;
			
			//Initialisation des addresses
			$shippingAddress = null;
			$billingAddress = null;
			
			//Lecture du rapport de ventes
			for ($i = 2; $i <= $this->_fileXls->sheets[0]['numRows']; $i++) {
				try{
					$cells = $this->_fileXls->sheets[0]['cells'][$i];
					$numOrder = $cells[self::C_ID_COMMANDE];
					
					if($error && $numOrder == $oldOrder){
					}
					//Nouvelle commande
					elseif($numOrder != $oldOrder){
						
	
						if(!empty($oldOrder) && !$error){
								
							//Ajoute le dernier produit 
							$productId = Mage::getModel('catalog/product')->getIdBySku($oldProduct);
							//Test si le produit existe
							if(!$productId){
								throw new Exception ( Mage::helper('tatvamarketplaces')->__("Le produit " . $oldProduct . " n'existe pas" ));
							}
							$product = Mage::getModel('catalog/product')->load($productId);
							
							if(!$taxPercent && $taxPercent == 0){
								$taxPercent = $this->getTaxPercent($shippingAddress,$billingAddress,$product);
							}
							$totalLigneTaxAmount = $totalLigneTTC - ($totalLigneTTC / (1 + ($taxPercent / 100)));
							$priceTTC = $totalLigneTTC / $qty;
							$priceHT = $priceTTC / (1 + ($taxPercent / 100));
							$this->addItem($product, $qty, $taxPercent, $priceHT, $totalLigneTTC, $totalLigneTaxAmount);

							//Frais de port				
							$shippingTaxAmount = $shippingAmount - ($shippingAmount / (1 + ($taxPercent / 100)));		
							$this->addShippingAmount($shippingAmount, $shippingTaxAmount,$taxPercent);
							
							//Totaux
							$totalTTC += $shippingAmount;
							$taxAmount = $totalTTC - ($totalTTC / (1 + ($taxPercent / 100)));
							$subTotal = $totalTTC - $taxAmount;
							$this->addTotals($totalTTC,$subTotal, $taxAmount,$taxPercent);
							
							//Données partenaire
							$this->addPartnerValues($oldOrder, $dateOrder);
						
							
							//Sauvegarde la commande en cours
							$this->_saveOrder();	
						}
						
						//Prépare une nouvelle commande
						$this->_prepareNewOrder();
							
						//Date de la commande
						$dateOrder = $cells[self::C_DATE];
						
						//Nouvelle commande
						$oldOrder = $numOrder;
						
						//Pays
						$country = Mage::getModel('directory/country')->loadByName(utf8_encode($cells[self::C_ACHETEUR_PAYS]));
						
						$this->addCustomer(
							'', 
							'', 
							utf8_encode($cells[self::C_ACHETEUR_NOM]),
							''			
						);				
						
						//Adresse de livraison
						$shippingAddress = $this->addShippingAddress(
							'', 
							'', 
							utf8_encode($cells[self::C_ACHETEUR_NOM]), 
							'',
							utf8_encode($cells[self::C_ACHETEUR_RUE]),
							utf8_encode($cells[self::C_ACHETEUR_CP]),
							utf8_encode($cells[self::C_ACHETEUR_VILLE]),
							$country->getId(),
							utf8_encode($cells[self::C_ACHETEUR_TEL])
						);
						
						//Adresse de facturation
						$billingAddress = $this->addBillingAddress(
							'', 
							'', 
							utf8_encode($cells[self::C_ACHETEUR_NOM]), 
							'',
							utf8_encode($cells[self::C_ACHETEUR_RUE]),
							utf8_encode($cells[self::C_ACHETEUR_CP]),
							utf8_encode($cells[self::C_ACHETEUR_VILLE]),
							$country->getId(),
							utf8_encode($cells[self::C_ACHETEUR_TEL])				
						);						
		
						//Méthode de livraison
						$this->addShippingMethod(utf8_encode($cells[self::C_MODE_ENVOI]), true);
						
						//Initialisation des totaux
						$totalTTC = 0;
						$shippingAmount = 0;
						$totalTaxAmount = 0;
						$taxPercent = false;
						
						//Initialisation du produit
						$oldProduct = "";
						$qty = 0;
						$totalLigneTTC = 0;
						$totalLigneTaxAmount = 0;
						
						$error = false;
					}		
				
					//Produit
					$skuProduct = $cells[self::C_SKU];
					
					//Nouveau produit
					if($skuProduct != $oldProduct){
						
						if(!empty($oldProduct)){
							$productId = Mage::getModel('catalog/product')->getIdBySku($oldProduct);
							
							//Test si le produit existe
							if(!$productId){
								throw new Exception ( Mage::helper('tatvamarketplaces')->__("Le produit " . $oldProduct . " n'existe pas" ));
							}
							$product = Mage::getModel('catalog/product')->load($productId);
							
							if(!$taxPercent){
								$taxPercent = $this->getTaxPercent($shippingAddress,$billingAddress,$product);
							}
							$totalLigneTaxAmount = $totalLigneTTC - ($totalLigneTTC / (1 + ($taxPercent / 100)));
							$priceTTC = $totalLigneTTC / $qty;
							$priceHT = $priceTTC / (1 + ($taxPercent / 100));
							$this->addItem($product, $qty, $taxPercent, $priceHT, $totalLigneTTC, $totalLigneTaxAmount);
						}
						//Commande
						$totalTaxAmount += $totalLigneTaxAmount; 
						
						//Produit
						$oldProduct = $skuProduct;
						$qty = 0;
						$totalLigneTTC = 0;
						$totalLigneTaxAmount = 0;
						$priceTTC = $cells[self::C_PRIX_VENTE];
						
					}
					
					$totalLigneTTC += $cells[self::C_PRIX_VENTE];
					$qty++;
					
					//Total de la commande
					$totalTTC += $cells[self::C_PRIX_VENTE];
					
					//Total frais de port
					$shippingAmount += $cells[self::C_FRAIS_PORT];
					
				}catch(Exception $e){
					$message = $oldOrder . " : " . $e->getMessage();
					$this->addError($message);
					$error = true;
				}
			}
			
			if(!$error){
				//Ajoute le dernier produit 
				$productId = Mage::getModel('catalog/product')->getIdBySku($oldProduct);
				//Test si le produit existe
				if(!$productId){
					throw new Exception ( Mage::helper('tatvamarketplaces')->__("Le produit " . $oldProduct . " n'existe pas" ));
				}
				$product = Mage::getModel('catalog/product')->load($productId);
				
				if(!$taxPercent && $taxPercent == 0){
					$taxPercent = $this->getTaxPercent($shippingAddress,$billingAddress,$product);
				}
				$totalLigneTaxAmount = $totalLigneTTC - ($totalLigneTTC / (1 + ($taxPercent / 100)));
				$priceHT = $priceTTC / (1 + ($taxPercent / 100));
				$this->addItem($product, $qty, $taxPercent, $priceHT, $totalLigneTTC, $totalLigneTaxAmount);
				
				
				//Frais de port		
				$shippingTaxAmount = $shippingAmount - ($shippingAmount / (1 + ($taxPercent / 100)));					
				$this->addShippingAmount($shippingAmount, $shippingTaxAmount,$taxPercent);
				
				//Totaux
				$totalTTC += $shippingAmount;
				$taxAmount = $totalTTC - ($totalTTC / (1 + ($taxPercent / 100)));
				$subTotal = $totalTTC - $taxAmount;
				$this->addTotals($totalTTC,$subTotal, $taxAmount,$taxPercent);
				
				//Données partenaire
				$this->addPartnerValues($oldOrder, $dateOrder);
			
				
				//Sauvegarde la commande en cours
				$this->_saveOrder();	
			}
		}
		
		//Archive du rapport
		$this->archive();

	}
	
	protected function archive(){
		foreach ( $this->_files as $file ) {
			$fileOld = $this->getSourcePath () . DS . $file;
			$fileNew = $this->getSourcePath () . DS .'old_'. $file . '_traite_' . date ( 'Ymd' ).date('Hi');
			copy($fileOld,$fileNew);
			unlink($fileOld);	
		}
		
		
	}
	
	/**
	 * Initialisation du nom du fichier
	 */
	protected function _initFilename() {
		$nameFile = $this->getConfigData ( 'tatvamarketplaces_2xmoinscher/orders/filename' );
		$pseudo = $this->getConfigData ( 'tatvamarketplaces_2xmoinscher/orders/pseudo' );
		
		$prefixFiles = $nameFile  . $pseudo. '_';
		$this->_files = array ();
		exec ( 'cd ' . $this->getSourcePath () . '; ls ' . $prefixFiles . '*', $this->_files );
		
		if (empty ( $this->_files )) {
			throw new Exception ( Mage::helper('tatvamarketplaces')->__("Aucun fichier trouvé" ));
		}
	}
    
	/**
	 * Initialisation du chemin du fichier
	 */
	protected function _initPath() {
		$value = $this->getConfigData ( 'tatvamarketplaces_2xmoinscher/orders/path' );
		if (empty ( $value )) {
			throw new Exception ( Mage::helper('tatvamarketplaces')->__("Le chemin du fichier n'est pas configuré" ));
		}
		$this->setSourcePath ( $value );
	}
	
	/**
	 * Charge le fichier
	 *
	 */
	protected function _prepareReadXls() {
		try{
			$file = $this->getSourcePath () . DS . $this->getSourceFile();
			if (! file_exists ( $file )) {
				throw new Exception ( "Le fichier $file n'existe pas" );
			}
			
			// ExcelFile($filename, $encoding);
			$this->_fileXls = new Spreadsheet_Excel_Reader();
	
			// Set output Encoding.
			$this->_fileXls->setOutputEncoding('UTF-8');
	
			$this->_fileXls->read($file);
		}catch(Exception $e){
			throw $e;
		}
	}
	
	/**
	 * Initialise la valeur du chemin
	 * @param $path
	 */
	protected function setSourcePath($path) {
		$this->_path = $path;
	}
	
	/**
	 * Initialise la valeur du nom du fichier
	 * @param $filename
	 */
	protected function setSourceFile($filename) {
		$this->_filename = $filename;
	}
	
	/**
	 * Retourne le nom du chemin
	 * @return string
	 */
	protected function getSourcePath() {
		return $this->_path;
	}
	
	/**
	 * Retourne le nom du fichier
	 * @return string
	 */
	protected function getSourceFile() {
		return $this->_filename;
	}
	
	/**
	 * Retourne le nom du fichier
	 * @return string
	 */
	static public function getPartnerCode() {
		return $this->_code;
	}
} 