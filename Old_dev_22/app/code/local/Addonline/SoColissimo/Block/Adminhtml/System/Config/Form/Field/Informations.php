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
 * @category Addonline
 * @package Addonline_SoColissimo
 * @copyright Copyright (c) 2014 Addonline
 * @author Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Block_Adminhtml_System_Config_Form_Field_Informations extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * Constructor
     */
    public function __ ()
    {
        $args = func_get_args();
        return false;
    }
    
    /*
     * (non-PHPdoc) @see Mage_Adminhtml_Block_System_Config_Form_Field::_getElementHtml()
     */
    protected function _getElementHtml (Varien_Data_Form_Element_Abstract $element)
    {
        $version = Mage::getConfig()->getNode('modules/Addonline_SoColissimo/version');

        $storeId = Mage::app()->getStore()->getStoreId();

        $moi = Mage::helper('addonline_licence');
        
        // on veut des infos sur la licence
        $module = Mage::getSingleton('socolissimo/observer');
        $licenceInfos = $moi->_9cd4777ae76310fd6977a5c559c51820($module, $storeId, false);
        
        $storeKey = $licenceInfos['keyOfStore'];
        
        $ts = array();
        
        $ts[] = 'Version: ' . $version;
        
        $ts[] = "Clé: " . $storeKey;
        
        // si le client a saisi au niveau du magasin une clé invalide mais au dessus on a une clé valide alors elle va
        // etre utilsé
        // et donc on a une clé valide meme si celle saisi est fausse.. donc on le signale sinon ca fait bizarre
        // de voir une clé bidon mais marquée comme valide
        if ($licenceInfos["isKeyValide"] && $storeKey != $licenceInfos['keyValideIs'] &&
             $licenceInfos['keyValideIs'] != "") {
            $ts[] = "Clé réellement utilisée: " . $licenceInfos['keyValideIs'];
        }
        
        if ($licenceInfos["isKeyValide"]) {
            if ($licenceInfos["isKeyMulti"]) {
                $ts[] = 'Licence: ' . $licenceInfos["keyIsForEan"] . " (multi sites)";
            } else {
                $ts[] = 'Licence: ' . $licenceInfos["keyIsForEan"] . " (mono site)";
            }
        } else {
            $ts[] = "Erreur: clé invalide";
        }
        
        $ts[] = "Licence Checker Version: " . $moi->getVersion();
        
        return implode("<br />", $ts);
    }
}
