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
class Addonline_SoColissimo_Block_Listrelais extends Mage_Core_Block_Template
{

    /**
     * @var array $_listRelais
     */
    private $_listRelais = array();

    /**
     * getter
     * @return array
     */
    public function getListRelais()
    {
        return $this->_listRelais;
    }

    /**
     * setter
     * @param array $list
     */
    public function setListRelais($list)
    {
        $this->_listRelais = $list;
    }
}
