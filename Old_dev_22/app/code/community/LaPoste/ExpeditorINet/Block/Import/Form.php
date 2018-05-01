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
class LaPoste_ExpeditorINet_Block_Import_Form extends Mage_Adminhtml_Block_Widget
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('expeditorinet/import/form.phtml');
    }

}
