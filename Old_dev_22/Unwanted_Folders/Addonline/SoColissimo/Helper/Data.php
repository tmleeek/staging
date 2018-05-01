<?php
/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Addonline_SoColissimo
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Module en mode Flexibilité ?
     * @return boolean
     */
    public function isFlexibilite()
    {
        
        $moi = Mage::helper('addonline_licence');
        
        // on veut des infos sur la licence
        $module = Mage::getModel('socolissimo/observer');
        
        $storeId = Mage::app()->getStore()->getStoreId();
        
        $licenceInfos = $moi->_9cd4777ae76310fd6977a5c559c51820($module, $storeId, false);
        
        $licence = $licenceInfos["keyIsForEanId"];
        
        if($licence == Addonline_SoColissimo_Model_Observer::CONTRAT_FLEXIBILITE || $licence == Addonline_SoColissimo_Model_Observer::CONTRAT_FLEXIBILITE_MULTI) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Type de Livraison à domicile (avec signature, sans signature, ou les deux )
     */
    public function getTypeLivraisonDomicile()
    {
        return Mage::getStoreConfig('carriers/socolissimo/domicile_signature');
    }

    /**
     * Poids du panier
     * @return number
     */
    public function getQuoteWeight()
    {
        // on récupère le poids du colis en gramme de type entier
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $weight = 0;
        foreach ($quote->getAllItems() as $item) {
            $weight += $item->getRowWeight();
        }
        $weight = round($weight * 1000);
        if ($weight == 0) {
            $weight = 1;
        }
        return $weight;
    }

    /**
     * Date de livraison
     * @return string
     */
    public function getShippingDate()
    {
        $shippingDate = new Zend_Date();
        if ($delay = Mage::getStoreConfig('carriers/socolissimo/shipping_period')) {
            $shippingDate->addDay($delay);
        } else {
            $shippingDate->addDay(1);
        }
        return $shippingDate->toString('dd/MM/yyyy');
    }
    
    /**
     * retourn un tableau des Modes de livraisons qui neccessite un Point Relais
     */
    public function getDeliveryModesNeedRegateCode()
    {
        // liste des modes de livraison où l'id du point relais esy obligatoire
        return array("A2P", "MRL", "CIT", "BPR", "ACP", "CDI", "CMT", "BDP");        
    }

    /**
     * URL pour l'API Google Maps
     * @return string
     */
    public function getUrlGoogleMaps(){
        return Mage::getStoreConfig('carriers/socolissimo/api_key_google_maps') != '' ?
            '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key='.Mage::getStoreConfig('carriers/socolissimo/api_key_google_maps').'&region=FR&v=3"></script>' :
            '<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false&region=FR&v=3"></script>';
    }

}
