<?php
/**
 * created : 05 oct. 2009
 * 
 * @category Tatva
 * @package Tatva_Cibleweb
 * @author emchaabelasri
 * @copyrightTatvaI - 2009 - http://wwwTatvai.com
 * 
 * EXIG : 
 * REG  : 
 */

/**
 * Description of the class
 * @packageTatvai_Cibleweb
 */

class Tatva_Cibleweb_Model_System_Config_Source_Type
{
	
	const LOCAL = 'local';
	const FTP   = 'ftp';

    public function toOptionArray()
    {
        return array(
            array('value'=>self::LOCAL, 'label'=>Mage::helper('cibleweb')->__('Local Server')),
            array('value'=>self::FTP,   'label'=>Mage::helper('cibleweb')->__('FTP')),
        );
    }

}