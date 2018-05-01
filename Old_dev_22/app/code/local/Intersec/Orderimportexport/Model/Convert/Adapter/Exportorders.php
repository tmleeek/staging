<?php
/**
 * ExportOrders.php
 * CommerceThemes @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commercethemes.com/LICENSE-M1.txt
 *
 * @category   Orders
 * @package    Exportorders
 * @copyright  Copyright (c) 2003-2009 CommerceThemes @ InterSEC Solutions LLC. (http://www.commercethemes.com)
 * @license    http://www.commercethemes.com/LICENSE-M1.txt
 */

class Intersec_Orderimportexport_Model_Convert_Adapter_Exportorders
    extends Mage_Eav_Model_Convert_Adapter_Entity
{
    const MULTI_DELIMITER = ' , ';

    /**
     * Product model
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_stores;
    protected $_attributes = array();
    protected $_billingAddressModel;
    protected $_shippingAddressModel;
    protected $_requiredFields = array();
    protected $_ignoreFields = array();
    protected $_billingFields = array();
    protected $_billingMappedFields = array();
    protected $_billingStreetFields = array();
    protected $_billingRequiredFields = array();
    protected $_shippingFields = array();
    protected $_shippingMappedFields = array();
    protected $_shippingStreetFields= array();
    protected $_shippingRequiredFields = array();
    protected $_addressFields = array();
    protected $_regions;
    protected $_websites;
    protected $_address = null;
    protected $_customerId = '';

    public function __construct()
    {

        $this->setVar('entity_type', 'sales/order');
    }

    public function load()
    {
        $addressType = $this->getVar('filter/addressType');
        $attrFilterArray = array();
        $attrFilterArray ['firstname']                  = 'like';
        $attrFilterArray ['lastname']                   = 'like';
        $attrFilterArray ['email']                      = 'like';
        $attrFilterArray ['group']                      = 'eq';
        /*
         * Fixing date filter from and to
         */
        if ($var = $this->getVar('filter/created_at/from')) {
            $this->setVar('filter/created_at/from', $var . ' 00:00:00');
        }

        if ($var = $this->getVar('filter/created_at/to')) {
            $this->setVar('filter/created_at/to', $var . ' 23:59:59');
        }
        $attrToDb = array(
            'group'                     => 'group_id',
            'customer_address/country'  => 'customer_address/country_id',
        );

        // Added store filter
        if ($storeId = $this->getStoreId()) {
            $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
            if ($websiteId) {
                $this->_filter[] = array(
                    'attribute' => 'website_id',
                    'eq'        => $websiteId
                );
            }
        }

        parent::setFilter($attrFilterArray, $attrToDb);
    }
}
