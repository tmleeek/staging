<?php
/**
 * LaPoste_ExpeditorINet
 * 
 * @category    LaPoste
 * @package     LaPoste_ExpeditorINet
 * @copyright   Copyright (c) 2010 La Poste
 * @author 	    Smile (http://www.smile.fr) & Jibé
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LaPoste_ExpeditorINet_Model_Config_Source_FileCharset
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'ISO-8859-1', 'label'=>Mage::helper('expeditorinet')->__('ISO-8859-1')),
            array('value'=>'UTF-8', 'label'=>Mage::helper('expeditorinet')->__('UTF-8'))
        );
    }
}
