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
 * @author : ALLAIRE Benjamin
 * @mail : benjamin@boostmyshop.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AutoCancelOrder_Block_Adminhtml_System_Config_Form_Fieldset_OrderStatusTable extends Mage_Adminhtml_Block_System_Config_Form_Field {

    /**
     * get order statuses
     * @return <type>
     */
    public function getOrderStatus() {

        
            // set null to enable all possible
     $_stateStatuses = array(
        Mage_Sales_Model_Order::STATE_NEW,
        Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
        Mage_Sales_Model_Order::STATE_PROCESSING,
        Mage_Sales_Model_Order::STATE_COMPLETE,
        Mage_Sales_Model_Order::STATE_CLOSED,
        Mage_Sales_Model_Order::STATE_CANCELED,
        Mage_Sales_Model_Order::STATE_HOLDED,
        Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW,
    );
        
        // get order statuses
       // $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        
         if ($_stateStatuses) {
            $statuses = Mage::getSingleton('sales/order_config')->getStateStatuses($_stateStatuses);
        }
        else {
            $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        }

        $options = array();

        $options[] = array(
            'value' => '',
            'label' => Mage::helper('adminhtml')->__('-- Please Select --')
        );

        foreach ($statuses as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );

            
        }
        return $options;
    }

    /**
     * render muliselect
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {

        $table = $this->getOrderStatus(); //echo'<pre>'; var_dump($table); echo'</pre>'; die("*");
        
        //default element
        $element = "<tr id=\"row_autocancelorder_general_apply_on_orders\"><td class=\"label\"><label for=\"autocancelorder_general_apply_on_orders\"> Apply On Orders</label></td>";
       
        $element .= "<td class=\"value\"><select id=\"autocancelorder_general_apply_on_orders\" name=\"groups[general][fields][apply_on_orders][value][]\" class=\" select multiselect\" size=\"10\" multiple=\"multiple\">";

       foreach ($table as $status) { 
  
           // ignore complete and canceled and closed status
           if( $status['value'] != "complete" && $status['value'] != "canceled" && $status['value'] != "closed" ){
                $element .= "<option value=\"" . $status['value'] . "\">" . $status['label'] . "</option>";
           }
        }
    
        $element .= "</select></td></tr>";

        return $element;
    }

}
