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
class LaPoste_ExpeditorINet_Block_Export_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'expeditorinet';
        $this->_controller = 'export_orders';
        $this->_headerText = Mage::helper('expeditorinet')->__('Export to Expeditor Inet');
        parent::__construct();
        $this->_removeButton('add');
    }

}
