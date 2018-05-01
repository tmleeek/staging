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
 * Used in creating options for Socolissimo Domicile signature
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Model_Adminhtml_System_Config_Source_Domicilesignature
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array(
            'value' => '0',
            'label' => Mage::helper('socolissimo')->__('Sans Signature')
        );
        $options[] = array(
            'value' => '1',
            'label' => Mage::helper('socolissimo')->__('Avec Signature')
        );
        $options[] = array(
            'value' => '2',
            'label' => Mage::helper('socolissimo')->__('Avec Signature ou sans signature au choix du client')
        );
        return $options;
    }
}
