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
 * Collection PeriodesFermeture
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Mysql4_Liberte_PeriodesFermeture_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    /* (non-PHPdoc)
     * @see Mage_Core_Model_Resource_Db_Collection_Abstract::_construct()
     */
    public function _construct()
    {
        $this->_init('socolissimo/liberte_periodesFermeture');
    }
}