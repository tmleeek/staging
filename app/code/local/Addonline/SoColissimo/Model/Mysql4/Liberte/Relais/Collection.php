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
 * Collection Relais
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Mysql4_Liberte_Relais_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    /* (non-PHPdoc)
     * @see Mage_Core_Model_Resource_Db_Collection_Abstract::_construct()
     */
    public function _construct()
    {
        $this->_init('socolissimo/liberte_relais');
    }

    /**
     * Retourne une lise de relais les plus proches
     * @param unknown $latitude
     * @param unknown $longitude
     * @param unknown $typesRelais
     * @param unknown $weight
     */
    public function prepareNearestByType($latitude, $longitude, $typesRelais, $weight)
    {
        
        // calcul de la distance d'un arc de cercle à la surface de la terre entre deux points coordonnées : http://fr.wikipedia.org/wiki/Orthodromie
        $formuleDistance = '(6371 * ACOS(COS(RADIANS(main_table.latitude)) * COS(RADIANS(' . $latitude . ')) * COS(RADIANS(' . $longitude . '-main_table.longitude)) + SIN(RADIANS(main_table.latitude)) * SIN(RADIANS(' . $latitude . '))))';
        
        $dateLivraison = new Zend_Date();
        if ($delai = Mage::getStoreConfig('carriers/socolissimo/shipping_period')) {
            $dateLivraison->addDay($delai);
        } else {
            $dateLivraison->addDay(1);
        }
        
        $this->getSelect()
            ->distinct()
            ->columns(array(
            'distance' => $formuleDistance
        ))
            ->where('type_relais IN (?)', $typesRelais);
        
        $this->getSelect()->where('point_max > ?', $weight);
        
        $anneeLivraison = $dateLivraison->get(Zend_Date::YEAR);
        $dateLivraisonDB = $dateLivraison->toString('yyyy-MM-dd');
        // jointure sur le table des horaires : on selectionne tous ses champs et on filtre sur la date de livraison
        $this->getSelect()
            ->join(array(
            'h' => $this->getTable('socolissimo/liberte_horairesOuverture')
        ), 'main_table.id_relais = h.id_relais_ho', '*')
            ->where("STR_TO_DATE(concat(h.deb_periode_horaire , '/$anneeLivraison'), '%d/%m/%Y') <= ?", $dateLivraisonDB)
            ->where("STR_TO_DATE(concat(h.fin_periode_horaire , '/$anneeLivraison'), '%d/%m/%Y') >= ?", $dateLivraisonDB);
        
        // jointure sur le table des periodes de fermeture (pour exclure les relais fermés à la date de livraison)
        $this->getSelect()
            ->joinLeft(array(
            'f' => $this->getTable('socolissimo/liberte_periodesFermeture')
        ), 'main_table.id_relais = f.id_relais_fe', array())
            ->where("f.deb_periode_fermeture IS NULL OR f.deb_periode_fermeture > STR_TO_DATE('$dateLivraisonDB', '%Y-%m-%d') OR f.fin_periode_fermeture < STR_TO_DATE('$dateLivraisonDB', '%Y-%m-%d')");
        
        $this->getSelect()
            ->order('distance')
            ->limit(10);
        // Mage::log($this->getSelect()->__toString(), null, 'socolissimo.log');
    }
}