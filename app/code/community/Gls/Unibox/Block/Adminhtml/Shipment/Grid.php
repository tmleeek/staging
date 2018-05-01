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
class Gls_Unibox_Block_Adminhtml_Shipment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ShipmentGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getModel('glsbox/shipment')->getCollection());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('glsbox')->__('ID'),
            'align'     => 'right',
            'index'     => 'id',
            'type'      => 'number',
        ));
        
        $this->addColumn('shipment_id', array(
            'header'    => Mage::helper('glsbox')->__('ShipmentId'),
            'align'     => 'left',
            'index'     => 'shipment_id',
        ));		

        $this->addColumn('kundennummer', array(
            'header'    => Mage::helper('glsbox')->__('Kundennummer'),
            'align'     => 'left',
            'index'     => 'kundennummer',
        ));
		
        $this->addColumn('customerid', array(
            'header'    => Mage::helper('glsbox')->__('Customer Id'),
            'align'     => 'left',
            'index'     => 'customerid',
        ));		
		
        $this->addColumn('contactid', array(
            'header'    => Mage::helper('glsbox')->__('Contact Id'),
            'align'     => 'left',
            'index'     => 'contactid',
        ));

        $this->addColumn('depotnummer', array(
            'header'    => Mage::helper('glsbox')->__('DepotNummer'),
            'align'     => 'left',
            'index'     => 'depotnummer',
        ));

        $this->addColumn('depotcode', array(
            'header'    => Mage::helper('glsbox')->__('DepotCode'),
            'align'     => 'left',
            'index'     => 'depotcode',
        ));	

        $this->addColumn('weight', array(
            'header'    => Mage::helper('glsbox')->__('Gewicht'),
            'index'     => 'weight',
        ));

        $this->addColumn('paketnummer', array(
            'header'    => Mage::helper('glsbox')->__('Paketnummer'),
            'align'     => 'left',
            'index'     => 'paketnummer',
        ));		
		
        $this->addColumn('service', array(
            'header'    => Mage::helper('glsbox')->__('Service'),
            'align'     => 'left',
            'index'     => 'service',
        ));

        $this->addColumn('additional_service', array(
            'header'    => Mage::helper('glsbox')->__('ZusatzLeistungen'),
            'index'     => 'additional_service',

        ));

        $this->addColumn('gls_message', array(
            'header'    => Mage::helper('glsbox')->__('Gls Unibox Rückgabestring'),
            'align'     => 'left',
            'index'     => 'gls_message',
        ));	

        $this->addColumn('storniert', array(
            'header'    => Mage::helper('glsbox')->__('Storniert'),
            'index'     => 'storniert',
			'type'      => 'options',			
			'options'   => array(
				1 => 'Ja',
				0 => 'Nein',
				),
        ));		
		
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('glsbox')->__('Erstellt am'),
            'align'     => 'left',
            'index'     => 'created_at',
        ));	

		$this->addColumn('notes', array(
            'header'    => Mage::helper('glsbox')->__('Notiz'),
            'align'     => 'left',
            'index'     => 'notes',
        ));

        Mage::dispatchEvent('glsbox_adminhtml_grid_prepare_columns', array('block'=>$this));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
		return false;
    }
}