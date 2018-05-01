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
 * Classe relais commune au deux mode Liberte et Flexibilite
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
abstract class Addonline_SoColissimo_Model_Relais_Abstract extends Mage_Core_Model_Abstract
{

    const TYPE_POSTE = 'poste';

    const TYPE_COMMERCANT = 'commercant';

    /**
     * renvoie le type de relais
     * @return string
     */
    public function getType()
    {
        if (! $this->hasData('type')) {
            if ($this->getTypeRelais() == 'BPR' || $this->getTypeRelais() == 'CDI' || $this->getTypeRelais() == 'ACP' || $this->getTypeRelais() == 'BDP') {
                $this->setData('type', self::TYPE_POSTE);
            } elseif ($this->getTypeRelais() == 'A2P' || $this->getTypeRelais() == 'CMT') {
                $this->setData('type', self::TYPE_COMMERCANT);
            } else {
                $this->setData('type', '');
            }
        }
        return $this->getData('type');
    }

    /**
     * isBureauPoste
     * @return boolean
     */
    public function isBureauPoste()
    {
        return $this->getType() == self::TYPE_POSTE;
    }

    /**
     * isCommercant
     * @return boolean
     */
    public function isCommercant()
    {
        return $this->getType() == self::TYPE_COMMERCANT;
    }

    /**
     * isParking
     * @return boolean
     */
    public function isParking()
    {
        if (! $this->hasData('parking')) {
            $this->setParking($this->getTypeRelais() == 'CDI' || $this->getTypeRelais() == 'ACP');
        }
        return $this->getParking();
    }

    /**
     * isManutention
     * @return boolean
     */
    public function isManutention()
    {
        if (! $this->hasData('manutention')) {
            $this->setManutention($this->getTypeRelais() == 'CDI' || $this->getTypeRelais() == 'ACP');
        }
        return $this->getManutention();
    }
}