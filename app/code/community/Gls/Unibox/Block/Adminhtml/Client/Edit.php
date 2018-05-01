<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
class Gls_Unibox_Block_Adminhtml_Client_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'glsbox';
        $this->_controller = 'adminhtml_client';

        $this->_updateButton('save', 'label', Mage::helper('glsbox')->__('Speichern'));
        $this->_updateButton('delete', 'label', Mage::helper('glsbox')->__('Löschen'));

        if( $this->getRequest()->getParam($this->_objectId) ) {
            $model = Mage::getModel('glsbox/client')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('client_data', $model);
        }
    }

    public function getHeaderText()
    {
        if( Mage::registry('client_data') && Mage::registry('client_data')->getId() ) {
            return Mage::helper('glsbox')->__("Mandanten bearbeiten", $this->htmlEscape(Mage::registry('client_data')->getTitle()));
        } else {
            return Mage::helper('glsbox')->__('Neuen Mandanten');
        }
    }
}