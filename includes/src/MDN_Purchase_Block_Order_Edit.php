<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Purchase_Block_Order_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    private $_order;

    /**
     * Constructeur: on charge le devis
     *
     */
    public function __construct() {
        $this->_objectId = 'id';
        $this->_controller = 'order';
        $this->_blockGroup = 'Purchase';

        parent::__construct();

        if ($this->getOrder()->currentUserCanEdit()) {
            $this->_updateButton('save', 'onclick', 'beforeSavePurchaseOrder(0)');
        }
        else
            $this->_removeButton('save');
        if (!Mage::getSingleton('admin/session')->isAllowed('admin/erp/purchasing/purchase_orders/edit'))
            $this->_removeButton('save');

        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/purchasing/purchase_orders/print')) {
            $this->_addButton(
                    'print', array(
                'label' => Mage::helper('purchase')->__('Print'),
                'onclick' => "window.location.href='" . $this->getUrl('Purchase/Orders/Print') . 'po_num/' . $this->getOrder()->getId() . "'",
                'level' => -1
                    )
            );
        }

        if ($this->getOrder()->currentUserCanEdit()) {
            if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/purchasing/purchase_orders/import_supply_needs')) {
                $this->_addButton(
                        'import', array(
                    'label' => Mage::helper('purchase')->__('Import Supply Needs'),
                    'onclick' => "window.open('" . $this->getUrl('Purchase/Orders/ImportFromSupplyNeeds', array('po_num' => $this->getOrder()->getId())) . "', '');",
                    'level' => -1
                        )
                );
            }

            if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/purchasing/purchase_orders/scanner_delivery')) {
                $this->_addButton(
                        'scanner_delivery', array(
                    'label' => Mage::helper('purchase')->__('Scanner delivery'),
                    'onclick' => "document.location.href = '" . $this->getUrl('Purchase/Orders/ScannerDelivery', array('po_num' => $this->getOrder()->getId())) . "';",
                    'level' => -1
                        )
                );
            }
        }

        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/purchasing/purchase_orders/print')) {
            $this->_addButton(
                    'export', array(
                'label' => Mage::helper('purchase')->__('Export'),
                'onclick' => "window.open('" . $this->getUrl('Purchase/Orders/csvExport', array('po_num' => $this->getOrder()->getId())) . "', '');",
                'level' => -1
                    )
            );
        }

        if ($this->getOrder()->currentUserCanEdit()) {
            if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/purchasing/purchase_orders/delete')) {
                $this->_addButton(
                        'delete', array(
                    'label' => Mage::helper('purchase')->__('Delete'),
                    'onclick' => "if (window.confirm('" . Mage::helper('purchase')->__('Are you sure ?') . "')) {document.location.href='" . $this->getUrl('Purchase/Orders/delete', array('po_num' => $this->getOrder()->getId())) . "';}",
                    'level' => -1,
                    'class' => 'delete'
                        )
                );
            }
        }

        if (Mage::getSingleton('admin/session')->isAllowed('admin/erp/purchasing/purchase_orders/edit')) {
                if ($this->getOrder()->currentUserCanEdit()) {
                    $this->_addButton(
                            'save_and_lock', array(
                        'label' => Mage::helper('purchase')->__('Save and lock'),
                        'onclick' => "beforeSavePurchaseOrder(1)",
                        'level' => -1,
                        'class' => 'save'
                            )
                    );
            }
        }
    }

    public function getHeaderText() {
        return $this->__('Purchase Order #') . $this->getOrder()->getpo_order_id() . ' (' . $this->getOrder()->getSupplier()->getsup_name() . ')';
    }

    /**
     * 
     */
    public function GetBackUrl() {
        return $this->getUrl('Purchase/Orders/List', array());
    }

    /**
     * 
     * @return type
     */
    public function getOrder() {
        if ($this->_order == null) {
            $this->_order = mage::getModel('Purchase/Order')->load($this->getRequest()->getParam('po_num'));
        }
        return $this->_order;
    }

    /**
     * 
     * @return type
     */
    public function getSaveUrl() {
        return $this->getUrl('Purchase/Orders/Save');
    }

}