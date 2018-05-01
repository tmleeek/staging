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
class LaPoste_ExpeditorINet_Model_Config_Source_EndOfLineCharacter
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'lf', 'label'=>Mage::helper('expeditorinet')->__('LF')),
            array('value'=>'cr', 'label'=>Mage::helper('expeditorinet')->__('CR')),
            array('value'=>'crlf', 'label'=>Mage::helper('expeditorinet')->__('CR+LF'))
        );
    }
}
