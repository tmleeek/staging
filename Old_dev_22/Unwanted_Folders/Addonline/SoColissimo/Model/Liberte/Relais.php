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
 * Objet model Relais pour mode Liberté
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Liberte_Relais extends Addonline_SoColissimo_Model_Relais_Abstract
{

    /* (non-PHPdoc)
     * @see Varien_Object::_construct()
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('socolissimo/liberte_relais');
    }

    /**
     * Charge un relais
     * @param string $identifiant
     * @param string $reseau
     */
    public function loadByIdentifiantReseau($identifiant, $reseau)
    {
        $collection = $this->getCollection();
        $collection->addFieldToFIlter('identifiant', $identifiant);
        $collection->addFieldToFIlter('code_reseau', $reseau);
        return $collection->getFirstItem();
    }

    /**
     * getLibelle
     * @return string
     */
    public function getLibelle()
    {
        if (Mage::app()->getStore()->getLanguageCode() == 'NL') {
            return $this->getData('libelle_nl');
        } else {
            return $this->getData('libelle');
        }
    }

    /**
     * getAdresse
     * @return string
     */
    public function getAdresse()
    {
        if (Mage::app()->getStore()->getLanguageCode() == 'NL') {
            return $this->getData('adresse_nl');
        } else {
            return $this->getData('adresse');
        }
    }

    /**
     * getCommune
     * @return string
     */
    public function getCommune()
    {
        if (Mage::app()->getStore()->getLanguageCode() == 'NL') {
            return $this->getData('commune_nl');
        } else {
            return $this->getData('commune');
        }
    }
}